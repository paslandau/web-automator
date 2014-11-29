<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;

use paslandau\BooleanExpressions\ExpressionInterface;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Recovery\RecoveryStrategyInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\FinalSubmissionStepInterface;

interface FinalStepBuild
{
    /**
     * @return FinalSubmissionStepInterface
     */
    public function build();
}

interface FinalStepIdentifyBy
{
    /**
     * @param ExpressionInterface $expression
     * @return FinalStepReturnDataWith
     */
    public function identifyBy(ExpressionInterface $expression);
}

interface FinalStepReturnDataWith
{
    /**
     * @param DataExtractorInterface $extractor
     * @return FinalStepFailOn
     */
    public function returnDataWith(DataExtractorInterface $extractor);
}

interface FinalStepFailOn extends FinalStepBuild
{
    /**
     * @param ExpressionInterface $expression
     * @return FinalStepExtractWith
     */
    public function failOn(ExpressionInterface $expression);

    /**
     * @return FinalStepBuild
     */
    public function doNotFail();
}

interface FinalStepExtractWith
{
    /**
     * @param DataExtractorInterface $extractor
     * @return FinalStepRecoverWith
     */
    public function andExtractWith(DataExtractorInterface $extractor);

    /**
     * @return FinalStepRecoverWith
     */
    public function andExtractNothing();
}

interface FinalStepRecoverWith
{
    /**
     * @param RecoveryStrategyInterface $recovery
     * @return FinalStepBuild
     */
    public function recoverWith(RecoveryStrategyInterface $recovery);

    /**
     * @return FinalStepBuild
     */
    public function doNotRecover();
}

interface FinalStepBuilderBuildOrderInterface extends FinalStepIdentifyBy, FinalStepReturnDataWith, FinalStepFailOn, FinalStepExtractWith, FinalStepRecoverWith
{

} 