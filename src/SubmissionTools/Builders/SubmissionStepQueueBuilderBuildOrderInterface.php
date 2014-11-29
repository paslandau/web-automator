<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\WebAutomator\SubmissionTools\Submission\Steps\FinalSubmissionStepInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepQueueInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;

interface SubmissionStepQueueBuildInterface
{
    /**
     * @return SubmissionStepQueueInterface
     */
    public function build();
}

interface SubmissionStepQueueStartAtInterface
{
    /**
     * @param RequestInfoInterface $initialRequest
     * @return SubmissionStepQueueSetStepsInterface
     */
    public function startAt(RequestInfoInterface $initialRequest);
}

interface SubmissionStepQueueSetStepsInterface
{
    /**
     * @param SubmissionStepInterface[] $steps
     * @return SubmissionStepQueueUseDefaultsFromInterface|SubmissionStepQueueSetStepsInterface
     */
    public function setSteps(array $steps);

    /**
     * @param FinalSubmissionStepInterface $step
     * @return SubmissionStepQueueUseDefaultsFromInterface
     */
    public function setFinalStep(FinalSubmissionStepInterface $step);

    /**
     * @param SubmissionStepInterface $step
     * @return SubmissionStepQueueSetStepsInterface
     */
    public function setStep(SubmissionStepInterface $step);
}

interface SubmissionStepQueueUseDefaultsFromInterface extends SubmissionStepQueueBuildInterface
{
    /**
     * @param RequestInfoInterface $defaultRequest
     * @return SubmissionStepQueueBuildInterface
     */
    public function useDefaultsFrom(RequestInfoInterface $defaultRequest);
}


interface SubmissionStepQueueBuilderBuildOrderInterface extends SubmissionStepQueueStartAtInterface, SubmissionStepQueueSetStepsInterface, SubmissionStepQueueUseDefaultsFromInterface
{
} 