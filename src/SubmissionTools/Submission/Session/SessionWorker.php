<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Session;


use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepResultInterface;

class SessionWorker implements SessionWorkerInterface
{

    /**
     * @var int
     */
    private $sessionId;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var int|string
     */
    private $dataId;

    /**
     * @var mixed[]
     */
    private $data;

    /**
     * @var SubmissionStepResultInterface[]|null
     */
    private $result;

    /**
     * @var \Exception|null
     */
    private $error;

    function __construct($sessionId, SessionInterface $session = null, $dataId = null, array $data = null)
    {
        $this->data = $data;
        $this->dataId = $dataId;
        $this->session = $session;
        $this->sessionId = $sessionId;
        $this->result = null;
        $this->error = null;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getDataId()
    {
        return $this->dataId;
    }

    /**
     * @param mixed $dataId
     */
    public function setDataId($dataId)
    {
        $this->dataId = $dataId;
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param mixed $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return \Exception|null mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param \Exception|null $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return SubmissionStepResultInterface[]|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param SubmissionStepResultInterface[]|null $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}