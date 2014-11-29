<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Events;


use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepInterface;
use Symfony\Component\EventDispatcher\Event;

class SubmissionEvent extends Event{
    /**
     * @var SubmissionStepInterface
     */
    private $emitter;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @param SubmissionStepInterface $emitter
     * @param mixed $data
     */
    function __construct(SubmissionStepInterface $emitter, $data = null)
    {
        $this->emitter = $emitter;
        $this->data = $data;
    }

    /**
     * @param mixed $emitter
     */
    public function setEmitter($emitter)
    {
        $this->emitter = $emitter;
    }

    /**
     * @return mixed
     */
    public function getEmitter()
    {
        return $this->emitter;
    }

    /**
     * @param mixed $data|null
     */
    public function setData($data = null)
    {
        $this->data = $data;
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }
} 