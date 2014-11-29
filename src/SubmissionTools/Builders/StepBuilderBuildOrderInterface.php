<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;

use paslandau\BooleanExpressions\ExpressionInterface;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoBuilder;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\IntermediateSubmissionStepInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Recovery\RecoveryStrategyInterface;

interface StepBuild
{
    /**
     * @return IntermediateSubmissionStepInterface
     */
    public function build();
}

interface StepIdentifyBy
{
    /**
     * @param ExpressionInterface $expression
     * @return StepBuildNextRequestBy
     */
    public function identifyBy(ExpressionInterface $expression);
}

interface StepBuildNextRequestBy
{
    /**
     * @param RequestInfoBuilder $builder
     * @return StepReturnDataWith
     */
    public function buildNextRequestBy(RequestInfoBuilder $builder);
}

interface StepReturnDataWith extends StepBuild
{
    /**
     * @param DataExtractorInterface $extractor
     * @return StepFailOn
     */
    public function returnDataWith(DataExtractorInterface $extractor);

    /**
     * @return StepFailOn
     */
    public function returnNothing();
}

interface StepFailOn extends StepBuild
{
    /**
     * @param ExpressionInterface $expression
     * @return StepExtractWith
     */
    public function failOn(ExpressionInterface $expression);

    /**
     * @return StepBuild
     */
    public function doNotFail();
}

interface StepExtractWith
{
    /**
     * @param DataExtractorInterface $extractor
     * @return StepRecoverWith
     */
    public function andExtractWith(DataExtractorInterface $extractor);

    /**
     * @return StepRecoverWith
     */
    public function andExtractNothing();
}

interface StepRecoverWith
{
    /**
     * @param RecoveryStrategyInterface $recovery
     * @return StepBuild
     */
    public function recoverWith(RecoveryStrategyInterface $recovery);

    /**
     * @return StepBuild
     */
    public function doNotRecover();
}

interface StepBuilderBuildOrderInterface extends StepIdentifyBy, StepBuildNextRequestBy, StepReturnDataWith, StepFailOn, StepExtractWith, StepRecoverWith {

} 