<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Identification\IdentificationInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\IdentificationExtraction\IdentificationExtractionInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Recovery\RecoveryStrategyInterface;

class FinalSubmissionStep extends AbstractBaseSuccessfulStep implements FinalSubmissionStepInterface
{

    /**
     * @param IdentificationInterface $identification
     * @param DataExtractorInterface $returnExtractor
     * @param IdentificationExtractionInterface $failExtractor [optional]. Default: null.
     * @param RecoveryStrategyInterface $recoveryStrategy [optional]. Default: null.
     */
    function __construct(IdentificationInterface $identification, DataExtractorInterface $returnExtractor, IdentificationExtractionInterface $failExtractor = null, RecoveryStrategyInterface $recoveryStrategy = null)
    {
        parent::__construct($identification, $returnExtractor, $failExtractor, $recoveryStrategy);
    }
}