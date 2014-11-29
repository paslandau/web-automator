<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\IdentificationExtraction;


use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Identification\IdentificationInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class IdentificationExtractor implements IdentificationExtractionInterface {

    /**
     * @var IdentificationInterface
     */
    private $identification;

    /**
     * @var DataExtractorInterface
     */
    private $extractor;

    /**
     * @param IdentificationInterface $identification
     * @param DataExtractorInterface $extractor
     */
    function __construct(IdentificationInterface $identification, DataExtractorInterface $extractor)
    {
        $this->identification = $identification;
        $this->extractor = $extractor;
    }

    public function identify(ResponseDataInterface $responseData)
    {
        return $this->identification->isIdentifiedBy($responseData);
    }

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return mixed
     */
    public function extract(ResponseDataInterface $responseData)
    {
        $this->extractor->GetData($responseData);
    }

}