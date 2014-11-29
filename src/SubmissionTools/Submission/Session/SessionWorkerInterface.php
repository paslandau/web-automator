<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Session;


interface SessionWorkerInterface
{
    /**
     * @return int
     */
    public function getSessionId();

    /**
     * @return SessionInterface
     */
    public function getSession();

    /**
     * @return string|int
     */
    public function getDataId();

    /**
     * @todo introduce Data class?
     * @return mixed[]
     */
    public function getData();

    /**
     * @return \Exception|null mixed
     */
    public function getError();

    /**
     * @param \Exception|null $error
     */
    public function setError($error);

    /**
     * @return \paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepResultInterface[]|null
     */
    public function getResult();

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepResultInterface[]|null $result
     */
    public function setResult($result);
}