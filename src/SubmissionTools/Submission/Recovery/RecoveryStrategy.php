<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Recovery;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\SimpleRequestInfo;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoBuilderInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class RecoveryStrategy implements RecoveryStrategyInterface
{

    /**
     * @var RequestInfoBuilderInterface
     */
    private $requestInfoBuilder;

    /**
     * @var string
     */
    private $stepIndex;

    /**
     * @param RequestInfoBuilderInterface $requestInfoBuilder
     * @param null|string $stepIndex
     */
    function __construct(RequestInfoBuilderInterface $requestInfoBuilder, $stepIndex = null)
    {
        $this->requestInfoBuilder = $requestInfoBuilder;
        $this->stepIndex = $stepIndex;
    }

    /**
     * @return \paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoBuilderInterface
     */
    public function getRequestInfoBuilder()
    {
        return $this->requestInfoBuilder;
    }

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoBuilderInterface $requestInfoBuilder
     */
    public function setRequestInfoBuilder($requestInfoBuilder)
    {
        $this->requestInfoBuilder = $requestInfoBuilder;
    }

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return SimpleRequestInfo
     */
    public function getNextRequest(ResponseDataInterface $responseData)
    {
        return $this->requestInfoBuilder->buildRequestInfo($responseData);
    }

    /**
     * @return null|string
     */
    public function getStepIndex()
    {
        return $this->stepIndex;
    }

    /**
     * @param string $stepIndex
     */
    public function setStepIndex($stepIndex)
    {
        $this->stepIndex = $stepIndex;
    }
}