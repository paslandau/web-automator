<?php
namespace paslandau\WebAutomator\User\Account;

use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepResultInterface;

interface AccountWorkerInterface
{
    /**
     * @return AccountSubmissionData
     */
    public function getAccountData();

    /**
     * @return mixed
     */
    public function getDataId();

    /**
     * @return \Exception|null mixed
     */
    public function getError();

    /**
     * @return SubmissionStepResultInterface[]|null
     */
    public function getResult();

    /**
     * @param \Exception|null $error
     */
    public function setError($error);

    /**
     * @param SubmissionStepResultInterface[]|null $result
     */
    public function setResult($result);
}