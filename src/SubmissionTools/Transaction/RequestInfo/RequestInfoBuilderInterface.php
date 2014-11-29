<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo;

use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

interface RequestInfoBuilderInterface {

    /**
     * @param ResponseDataInterface $responseData
     * @return RequestInfoInterface
     */
    public function buildRequestInfo(ResponseDataInterface $responseData);
} 