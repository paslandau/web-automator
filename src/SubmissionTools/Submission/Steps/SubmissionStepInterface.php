<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\DataFiltering\Traits\HasEventDispatcherInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

interface SubmissionStepInterface extends HasEventDispatcherInterface{

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return boolean
     */
    public function isIdentifiedBy(ResponseDataInterface $responseData);

    /**
     * @return string
     */
    public function getId();
}