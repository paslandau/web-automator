<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\BooleanExpressions\ExpressionInterface;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Identification\BaseIdentifier;
use paslandau\WebAutomator\SubmissionTools\Transaction\IdentificationExtraction\IdentificationExtractor;
use paslandau\WebAutomator\SubmissionTools\Submission\Recovery\RecoveryStrategyInterface;

abstract class AbstractBaseSuccessfulStepBuilder extends AbstractBaseSubmissionStepBuilder{
    protected $onFail;
    protected $returnExtractor;
    protected $recovery;
    protected $failIdentifier;

    public function returnDataWith(DataExtractorInterface $extractor){
        $this->returnExtractor = $extractor;
        return $this;
    }

    public function returnNothing(){
        return $this;
    }

    public function failOn(ExpressionInterface $expression){
        $this->failIdentifier = new BaseIdentifier($expression, $this->id."_onFail");
        return $this;
    }

    public function doNotFail(){
        return $this;
    }

    public function andExtractWith(DataExtractorInterface $extractor){
        $this->onFail = new IdentificationExtractor($this->failIdentifier,$extractor);
        return $this;
    }

    public function andExtractNothing(){
        return $this;
    }

    public function recoverWith(RecoveryStrategyInterface $recovery){
        $this->recovery = $recovery;
        return $this;
    }

    public function doNotRecover(){
        return $this;
    }
}
