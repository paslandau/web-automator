<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\DataFiltering\Util\StringUtil;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Exceptions\SubmissionStepException;

class SubmissionStepResult implements SubmissionStepResultInterface {
    /**
     * @var \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface
     */
    private $response;

    /**
     * @var mixed
     */
    private $resultData;
    /**
     * @var SubmissionStepException
     */
    private $exception;

    function __construct(ResponseDataInterface $response, $resultData = null, $exception = null)
    {
        $this->response = $response;
        $this->exception = $exception;
        $this->resultData = $resultData;
    }

    /**
     * @return SubmissionStepException
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return null
     */
    public function getResultData()
    {
        return $this->resultData;
    }

    public function __toString(){
        $ss = StringUtil::GetObjectString($this);
        return $ss;
    }
}