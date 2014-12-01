<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission;


use paslandau\WebAutomator\SubmissionTools\Submission;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepResultInterface;
use paslandau\WebAutomator\User\Account\AccountWorkerInterface;
use Symfony\Component\EventDispatcher\Event;

class SubmissionResultEvent extends Event{
    /**
     * @var AccountWorkerInterface
     */
    private $worker;

    /**
     * @var SubmissionStepResultInterface[]
     */
    private $result;

    /**
     * @var \Exception
     */
    private $error;

    /**
     * @param AccountWorkerInterface $worker
     * @param SubmissionStepResultInterface[] $result
     * @param \Exception $error
     */
    function __construct(AccountWorkerInterface $worker = null, array $result = null, \Exception $error = null)
    {
        $this->worker = $worker;
        $this->error = $error;
        $this->result = $result;
    }

    /**
     * @return AccountWorkerInterface
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @return \Exception
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return SubmissionStepResultInterface[]
     */
    public function getResult()
    {
        return $this->result;
    }
}