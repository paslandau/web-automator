<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Identification\IdentificationInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\IdentificationExtraction\IdentificationExtractionInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Events\SubmissionEvent;
use paslandau\WebAutomator\SubmissionTools\Submission\Recovery\RecoveryStrategyInterface;

class AbstractBaseSuccessfulStep extends AbstractBaseSubmissionStep{
    /**
     * @var int
     */
    protected $maxIdentificationCount;

    /**
     * @var int
     */
    protected $identificationCount;

    /**
     * @var DataExtractorInterface
     */
    protected $returnExtractor;

    /**
     * @var IdentificationExtractionInterface
     */
    protected $failExtractor;

    /**
     * @var RecoveryStrategyInterface
     */
    protected $recoveryStrategy;

    /**
     * @var callable[]
     */
    private $onFail;

    /**
     * @param IdentificationInterface $identification
     * @param DataExtractorInterface $returnExtractor
     * @param IdentificationExtractionInterface $failExtractor
     * @param RecoveryStrategyInterface $recoveryStrategy
     */
    function __construct(IdentificationInterface $identification, DataExtractorInterface $returnExtractor = null, IdentificationExtractionInterface $failExtractor = null, RecoveryStrategyInterface $recoveryStrategy = null)
    {
        $this->returnExtractor = $returnExtractor;
        $this->failExtractor = $failExtractor;
        $this->recoveryStrategy = $recoveryStrategy;
        $this->onFail = array();
        $this->identificationCount = 0;
        $this->maxIdentificationCount = 1;
        parent::__construct($identification);
    }

    /**
     * @param ResponseDataInterface $responseData
     * @throws \RuntimeException
     * @return boolean
     */
    public function isIdentifiedBy(ResponseDataInterface $responseData)
    {
        $res = parent::isIdentifiedBy($responseData);
        if($res === true && $this->maxIdentificationCount > 0){
            $this->identificationCount++;
            if($this->identificationCount > $this->maxIdentificationCount){
                throw new \RuntimeException("Reached maximum number of identifications ({$this->maxIdentificationCount}) for this step!");
            }
        }
        return $res;
    }

    /**
     * @param ResponseDataInterface $responseData
     * @return boolean
     */
    public function process(ResponseDataInterface $responseData)
    {
        $res = true;
        $data = null;
        if($this->failExtractor !== null && $this->failExtractor->identify($responseData)){
            $res = false;
            $data = $this->failExtractor->extract($responseData);
            $this->emitOnFail($data);
        }
        return $res;
    }

    /**
     * @param callable $onFailCallback
     */
    public function attachOnFail(callable $onFailCallback)
    {
        $this->onFail[] = $onFailCallback;
    }

    /**
     * @param callable $onFailCallback
     */
    public function detachOnFail(callable $onFailCallback){
        $key = array_search($onFailCallback, $this->onFail);
        if ($key !== false) {
            unset($this->onFail[$key]);
        }
    }

    private function emitOnFail($data){
        $event = new SubmissionEvent($this,$data);
        foreach($this->onFail as $callable){
            $callable($event);
        }
    }

    /**
     * @param \paslandau\DataFiltering\Extraction\DataExtractorInterface $failExtractor
     */
    public function setFailExtractor($failExtractor = null)
    {
        $this->failExtractor = $failExtractor;
    }

    /**
     * @return \paslandau\DataFiltering\Extraction\DataExtractorInterface
     */
    public function getFailExtractor()
    {
        return $this->failExtractor;
    }   /**
 * @param \paslandau\DataFiltering\Identification\IdentificationInterface $identification
 */

    /**
     * @param int $identificationCount
     */
    public function setIdentificationCount($identificationCount)
    {
        $this->identificationCount = $identificationCount;
    }

    /**
     * @return int
     */
    public function getIdentificationCount()
    {
        return $this->identificationCount;
    }

    /**
     * @param int $maxIdentificationCount
     */
    public function setMaxIdentificationCount($maxIdentificationCount)
    {
        $this->maxIdentificationCount = $maxIdentificationCount;
    }

    /**
     * @return int
     */
    public function getMaxIdentificationCount()
    {
        return $this->maxIdentificationCount;
    }

    /**
     * @param ResponseDataInterface $responseData
     * @return mixed
     */
    public function getResult(ResponseDataInterface $responseData)
    {
        if($this->returnExtractor === null){
            return null;
        }
        return $this->returnExtractor->GetData($responseData);
    }

    /**
     * @param \paslandau\DataFiltering\Extraction\DataExtractorInterface $returnExtractor
     */
    public function setReturnExtractor($returnExtractor = null)
    {
        $this->returnExtractor = $returnExtractor;
    }

    /**
     * @return \paslandau\DataFiltering\Extraction\DataExtractorInterface
     */
    public function getReturnExtractor()
    {
        return $this->returnExtractor;
    }

    /**
     * @param ResponseDataInterface $responseData
     * @return null|\paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\SimpleRequestInfo
     */
    public function getRecoveryRequestInfo(ResponseDataInterface $responseData){
        if($this->recoveryStrategy === null){
            return null;
        }
        return $this->recoveryStrategy->getNextRequest($responseData);
    }

    /**
     * @param RecoveryStrategyInterface $strategy
     * @return void
     */
    public function setRecoveryStrategy(RecoveryStrategyInterface $strategy){
        $this->recoveryStrategy = $strategy;
    }

    /**
     * @return null|string
     */
    public function getRecoveryStepIndex(){
        if($this->recoveryStrategy === null){
            return null;
        }
        return $this->recoveryStrategy->getStepIndex();
    }
} 