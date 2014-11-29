<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\IdentificationExtraction;


use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

interface IdentificationExtractionInterface {

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return boolean
     */
    public function identify(ResponseDataInterface $responseData);

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return mixed
     */
    public function extract(ResponseDataInterface $responseData);
} 