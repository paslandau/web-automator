<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\WebAutomator\SubmissionTools\Submission\Exceptions\SubmissionStepException;
use paslandau\DataFiltering\Traits\LoggerTrait;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class SubmissionStepQueue implements SubmissionStepQueueInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    public $currentStepIndex;
    /**
     * @var RequestInfoInterface
     */
    private $defaultRequestInfo;
    /**
     * @var RequestInfoInterface
     */
    private $initialRequest;
    /**
     * @var RequestInfoInterface
     */
    private $nextRequest;
    /**
     * @var mixed[]
     */
    private $result;
    /**
     * @var SubmissionStepInterface[]
     */
    private $steps;
    /**
     * @var SubmissionStepException
     */
    private $exception;
    /**
     * @var bool
     */
    private $isInitial;

    /**
     * @param RequestInfoInterface $initialRequest
     * @param SubmissionStepInterface[] $steps [optional]. Default: null.
     * @param RequestInfoInterface $defaultRequestInfo
     */
    public function __construct(RequestInfoInterface $initialRequest, array $steps = null, RequestInfoInterface $defaultRequestInfo = null)
    {
        $this->initialRequest = $initialRequest;
        if ($steps === null) {
            $steps = array();
        }
        $this->steps = $steps;
        $this->defaultRequestInfo = $defaultRequestInfo;
        $this->reset();
    }

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @throws null|\paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\RequestException
     * @return boolean
     */
    public function evaluate(ResponseDataInterface $responseData)
    {
        if (count($this->steps) == 0) {
            throw new \RuntimeException("At least one SubmissionStep hast to be set!");
        }

        $result = false;
        if ($this->isInitial) {
            reset($this->steps);
            $curKey = key($this->steps);
            $this->currentStepIndex = $curKey;
            $this->isInitial = false;
        } else {
            $this->setCurrentStep();
            $curKey = key($this->steps);
            if (next($this->steps) === false) { //advance pointer
                reset($this->steps);
            }
        }
        $step = null;
        $thisStep = null;
        try {
            while (true) {
                /** @var SubmissionStepInterface $step */
                $step = current($this->steps);
                $thisStep = key($this->steps);
                $this->getLogger()->debug("Checking step '$thisStep'...");
                //todo handle Exceptions more elegant, e.g. make it possible for steps to evaluate an exception
                // currently we're making sure that $responseData's guzzle response is not null to avoid errors
                $error = $responseData->GetException();

                if ($error !== null || $step->isIdentifiedBy($responseData)) {
                    $stepResult = null;
                    try {
                        if ($error !== null) {
                            throw $error;
                        }

                        if ($step instanceof ErrorStepInterface) {
                            /** @var ErrorStepInterface $step */
                            throw new SubmissionStepException($curKey, $step, $this->getNextRequest(), $responseData, "Encountered error step '$thisStep'");
                        } else {
                            /** @var SuccessfulStepInterface $step */
                            if ($step->process($responseData)) {
                                $stepResult = $step->getResult($responseData);
                                $this->currentStepIndex = $thisStep;
                                $result = true;
                                if ($step instanceof IntermediateSubmissionStepInterface) {
                                    /** @var IntermediateSubmissionStepInterface $step */
                                    $next = $step->getNextRequestInfo($responseData);
                                    $this->nextRequest = $this->prepareRequest($next);
                                } elseif ($step instanceof FinalSubmissionStepInterface) {
                                    $this->nextRequest = null;
                                }
                            } else { // @todo: processing failed --- what to do now?
                                $next = $step->getRecoveryRequestInfo($responseData);
                                if ($next !== null) {
                                    $this->getLogger()->info("Failed processing step '$thisStep', retrying...");
                                    $this->nextRequest = $this->prepareRequest($next);
                                    $stepIndex = $step->getRecoveryStepIndex();
                                    if ($stepIndex !== null) {
                                        $thisStep = $stepIndex;
                                    }
                                    $this->currentStepIndex = $thisStep;
                                } else {
                                    $this->getLogger()->info("No recovery strategy found for '$thisStep', aborting.");
                                    throw new SubmissionStepException($curKey, $step, $this->getNextRequest(), $responseData, "Processing failed in step '$thisStep'. Could not find a RecoveryStrategy.");
                                }
                            }
                        }
                        // no exception occured, set result. This might be null if ->process failed, though
                        $this->setSuccessFor($thisStep, $responseData, $stepResult);
                        break;
                    } catch (\Exception $e) {
                        //something, somewhere went wrong
                        if (!($e instanceof SubmissionStepException)) {
                            $e = new SubmissionStepException($curKey, $step, $this->getNextRequest(), $responseData, "An error occured, previous step was '$curKey'", 0, $e);
                        }
                        $this->setFailFor($thisStep, $responseData, $e);
                        //rethrow to let the queue fail
                        throw $e;
                    }
                }

                $nextStep = next($this->steps);
                if ($nextStep === false && key($this->steps) === null) {
                    reset($this->steps);
                }
                $nextKey = key($this->steps);
                // @todo: Maybe allow to check the previus step again?
                if ($nextKey === $curKey) {
                    throw new SubmissionStepException($curKey, null, $this->getNextRequest(), $responseData, "No fitting step found, previous step was '$curKey'");
                }
            }
        } catch (SubmissionStepException $e) { // there shouldn't be any other exceptions since we're checking the inner loop by try catch and transform every Exception
            $this->fail($e);
            $result = false;
        }
        return $result;
    }

    /**
     * @param \Exception $e
     */
    public function fail(\Exception $e)
    {
        $this->nextRequest = null;
        $this->exception = $e;
    }

    public function reset()
    {
        $this->nextRequest = $this->prepareRequest($this->initialRequest);
        $this->isInitial = true;
        $this->currentStepIndex = null;
        reset($this->steps);
        $this->result = array();
        $this->exception = null;
    }

    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }

    public function getCurrentStepIndex()
    {
        return $this->currentStepIndex;
    }

    public function getNextRequest()
    {
        return $this->nextRequest;
    }

    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param string $index
     * @param SubmissionStepInterface $step
     */
    public function addStep($index, SubmissionStepInterface $step)
    {
        $this->steps[$index] = $step;
    }

    /**
     * @param string $index
     * @return null|SubmissionStepInterface
     */
    public function getStep($index)
    {
        if (array_key_exists($index, $this->steps)) {
            return $this->steps[$index];
        }
        return null;
    }

    /**
     * @param string $index
     */
    public function removeStep($index)
    {
        if (array_key_exists($index, $this->steps)) {
            unset($this->steps[$index]);
        }
    }

    /**
     * @return SubmissionStepInterface[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

    private function setCurrentStep()
    {
        $curKey = key($this->steps);
        $nextKey = $curKey;
        while ($nextKey !== $this->currentStepIndex) {
            if (next($this->steps) === false && key($this->steps) === null) {
                reset($this->steps);
            }
            $nextKey = key($this->steps);
            if ($nextKey === $curKey) {
                throw new \RuntimeException("'{$this->currentStepIndex}' not found in steps! Previous step was '$curKey'");
            }
        }
    }

    private function setSuccessFor($stepIndex, $response, $resultData = null)
    {
        $this->setResultFor($stepIndex, $response, $resultData);
    }

    private function setFailFor($stepIndex, $response, $exception)
    {
        $this->setResultFor($stepIndex, $response, null, $exception);
    }

    private function setResultFor($stepIndex, $response, $resultData = null, $exception = null)
    {
        $result = new SubmissionStepResult($response, $resultData, $exception);
        $this->result[$stepIndex] = $result;
    }

    private function prepareRequest(RequestInfoInterface $requestInfo)
    {
        $requestInfo->mergeWithDefaults($this->defaultRequestInfo);
        return $requestInfo;
    }
}