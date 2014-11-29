<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

interface IntermediateSubmissionStepInterface extends SuccessfulStepInterface{

    /**
     * @param ResponseDataInterface $responseData
     * @return RequestInfoInterface
     */
    public function getNextRequestInfo(ResponseDataInterface $responseData);
} 