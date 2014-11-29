<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\WebAutomator\SubmissionTools\Submission\Steps\FinalSubmissionStep;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\FinalSubmissionStepInterface;

/**
 * Class FinalStepBuilder
 */
class FinalStepBuilder extends AbstractBaseSuccessfulStepBuilder implements FinalStepBuilderBuildOrderInterface
{

    public static function init($id)
    {
        return new self($id);
    }

    /**
     * @return FinalSubmissionStepInterface
     */
    public function build()
    {
        $step = new FinalSubmissionStep($this->identifier, $this->returnExtractor, $this->onFail, $this->recovery);
        return $step;
    }
} 