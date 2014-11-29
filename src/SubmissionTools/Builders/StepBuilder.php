<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\WebAutomator\SubmissionTools\Submission\Steps\IntermediateSubmissionStep;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoBuilder;

/**
 * Class StepBuilder
 */
class StepBuilder extends AbstractBaseSuccessfulStepBuilder implements StepBuilderBuildOrderInterface
{

    private $requestInfoBuilder;

    /**
     * @param $id
     * @return StepIdentifyBy
     */
    public static function init($id)
    {
        return new self($id);
    }

    public function buildNextRequestBy(RequestInfoBuilder $builder)
    {
        $this->requestInfoBuilder = $builder;
        return $this;
    }

    public function build()
    {
        $step = new IntermediateSubmissionStep($this->requestInfoBuilder, $this->identifier, $this->returnExtractor, $this->onFail, $this->recovery);
        return $step;
    }
}