<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission;

use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepQueueInterface;

interface SubmitterInterface {

    /**
     * @param SubmissionStepQueueInterface[] $submissionQueueList
     * @return SubmissionStepQueueInterface[]
     */
    public function run(array $submissionQueueList);

    /**
     * @return int
     */
    public function getParallel();

    /**
     * @param int $parallel
     */
    public function setParallel($parallel);
}