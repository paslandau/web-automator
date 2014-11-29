<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\WebAutomator\SubmissionTools\Submission\Exceptions\SubmissionStepException;

interface SubmissionStepResultInterface
{
    /**
     * @return SubmissionStepException
     */
    public function getException();

    /**
     * @return \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface
     */
    public function getResponse();

    /**
     * @return null
     */
    public function getResultData();
} 