<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

interface SubmissionStepQueueInterface {

    /**
     * @param ResponseDataInterface $responseData
     * @return boolean
     */
    public function evaluate(ResponseDataInterface $responseData);

    /**
     * @return RequestInfoInterface
     */
    public function getNextRequest();

    /**
     * @return SubmissionStepResultInterface[]
     */
    public function getResult();

    /**
     * @param \Exception $e
     * @return void
     */
    public function fail(\Exception $e);

    /**
     * Returns null if no Exception is set
     * @return \Exception|null
     */
    public function getException();

    /**
     * @return string
     */
    public function getCurrentStepIndex();

    /**
     * @param string $index
     * @param SubmissionStepInterface $step
     * @return void
     */
    public function addStep($index, SubmissionStepInterface $step);

    /**
     * Returns null if $index was not found
     * @param string $index
     * @return SubmissionStepInterface
     */
    public function getStep($index);

    /**
     * @param string $index
     * @return void
     */
    public function removeStep($index);

    /**
     * @return SubmissionStepInterface[]
     */
    public function getSteps();
}