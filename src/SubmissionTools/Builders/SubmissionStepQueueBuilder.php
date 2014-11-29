<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\WebAutomator\SubmissionTools\Submission\Steps\FinalSubmissionStepInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepQueue;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;

class SubmissionStepQueueBuilder implements SubmissionStepQueueBuilderBuildOrderInterface
{
    /**
     * @var RequestInfoInterface
     */
    private $initialRequest;
    /**
     * @var SubmissionStepInterface[]
     */
    private $steps;

    /**
     * @var RequestInfoInterface
     */
    private $defaultRequest;

    private function __construct()
    {
        $this->steps = [];
    }

    public static function init()
    {
        return new self();
    }

    public function startAt(RequestInfoInterface $initialRequest)
    {
        $this->initialRequest = $initialRequest;
        return $this;
    }

    public function setSteps(array $steps)
    {
        $this->steps = $steps;
        return $this;
    }

    public function setStep(SubmissionStepInterface $step)
    {
        $this->steps[$step->getId()] = $step;
        return $this;
    }

    public function setFinalStep(FinalSubmissionStepInterface $step)
    {
        $this->steps[$step->getId()] = $step;
        return $this;
    }

    public function useDefaultsFrom(RequestInfoInterface $defaultRequest)
    {
        $this->defaultRequest = $defaultRequest;
        return $this;
    }

    public function build()
    {
        $q = new SubmissionStepQueue($this->initialRequest, $this->steps, $this->defaultRequest);
        return $q;
    }
} 