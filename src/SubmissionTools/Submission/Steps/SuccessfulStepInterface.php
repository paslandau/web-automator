<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\SimpleRequestInfo;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Recovery\RecoveryStrategyInterface;

interface SuccessfulStepInterface extends SubmissionStepInterface{
    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return boolean
     */
    public function process(ResponseDataInterface $responseData);

    /**
     * @param callable $func
     * @return void
     */
    public function attachOnFail(callable $func);

    /**
     * @param int $maxIdentificationCount
     */
    public function setMaxIdentificationCount($maxIdentificationCount);

    /**
     * @return int
     */
    public function getMaxIdentificationCount();

    /**
     * @param callable $func
     * @return void
     */
    public function detachOnFail(callable $func);

    /**
     * @param ResponseDataInterface $responseData
     * @return mixed
     */
    public function getResult(ResponseDataInterface $responseData);

    /**
     * @param RecoveryStrategyInterface $strategy
     * @return mixed
     */
    public function setRecoveryStrategy(RecoveryStrategyInterface $strategy);

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return null|SimpleRequestInfo
     */
    public function getRecoveryRequestInfo(ResponseDataInterface $responseData);

    /**
     * @return null|string
     */
    public function getRecoveryStepIndex();
} 