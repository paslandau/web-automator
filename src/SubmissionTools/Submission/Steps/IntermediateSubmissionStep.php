<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Identification\IdentificationInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\IdentificationExtraction\IdentificationExtractionInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoBuilderInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Recovery\RecoveryStrategyInterface;

class IntermediateSubmissionStep extends AbstractBaseSuccessfulStep implements IntermediateSubmissionStepInterface
{
    /**
     * @var RequestInfoBuilderInterface
     */
    private $requestInfoBuilder;

    /**
     * @param RequestInfoBuilderInterface $requestInfoBuilder
     * @param IdentificationInterface $identification
     * @param DataExtractorInterface $returnExtractor [optional]. Default: null.
     * @param IdentificationExtractionInterface $failExtractor [optional]. Default: null.
     * @param RecoveryStrategyInterface $recoveryStrategy [optional]. Default: null.
     */
    public function __construct(RequestInfoBuilderInterface $requestInfoBuilder, IdentificationInterface $identification, DataExtractorInterface $returnExtractor = null, IdentificationExtractionInterface $failExtractor = null, RecoveryStrategyInterface $recoveryStrategy = null)
    {
        $this->requestInfoBuilder = $requestInfoBuilder;
        parent::__construct($identification, $returnExtractor, $failExtractor, $recoveryStrategy);
    }

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return RequestInfoInterface
     */
    public function getNextRequestInfo(ResponseDataInterface $responseData)
    {
        return $this->requestInfoBuilder->BuildRequestInfo($responseData);
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


}