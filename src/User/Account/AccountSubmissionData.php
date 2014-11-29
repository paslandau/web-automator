<?php

namespace paslandau\WebAutomator\User\Account;


class AccountSubmissionData {

    /**
     * @var AccountInterface
     */
    private $account;

    /**
     * @var mixed[]
     */
    private $data;

    function __construct($account, $data = null)
    {
        $this->account = $account;
        if($data === null){
            $data = [];
        }
        $this->data = $data;
    }

    /**
     * @return AccountInterface
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param AccountInterface $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return \mixed[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \mixed[] $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}