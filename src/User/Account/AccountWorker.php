<?php

namespace paslandau\WebAutomator\User\Account;


use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepResultInterface;

class AccountWorker implements AccountWorkerInterface
{
    /**
     * @var int|string
     */
    private $dataId;

    /**
     * @var AccountSubmissionData
     */
    private $accountData;

    /**
     * @var SubmissionStepResultInterface[]|null
     */
    private $result;

    /**
     * @var \Exception|null
     */
    private $error;

    /**
     * @param null $dataId
     * @param AccountSubmissionData $accountData
     */
    function __construct($dataId, AccountSubmissionData $accountData = null)
    {
        $this->accountData = $accountData;
        $this->dataId = $dataId;
        $this->result = null;
        $this->error = null;
    }

    /**
     * @return AccountSubmissionData
     */
    public function getAccountData()
    {
        return $this->accountData;
    }

    /**
     * @param mixed AccountSubmissionData
     */
    public function setAccountData(AccountSubmissionData $data)
    {
        $this->accountData = $data;
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