<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Recovery;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\SimpleRequestInfo;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

interface RecoveryStrategyInterface
{

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return SimpleRequestInfo
     */
    public function getNextRequest(ResponseDataInterface $responseData);

    /**
     *
     * @return null|string
     */
    public function getStepIndex();
}