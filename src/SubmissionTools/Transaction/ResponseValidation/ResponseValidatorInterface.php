<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

interface ResponseValidatorInterface {

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $response
     * @return boolean
     */
    public function isValid(ResponseDataInterface $response);

    /**
     * @param RequestInfoInterface &$request
     * @return void
     */
    public function fixRequest(RequestInfoInterface &$request);
} 