<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Exceptions;

use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepInterface;

class SubmissionStepException extends \Exception{
    /**
     * @var string
     */
    private $previousStepIndex;

    /**
     * @var SubmissionStepInterface
     */
    private $step;

    /**
     * @var \paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface
     */
    private $requestInfo;

    /**
     * @var \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface
     */
    private $responseData;

    public function __construct($previousStepIndex, SubmissionStepInterface $step = null, RequestInfoInterface $requestInfo = null, ResponseDataInterface $responseData = null, $message = null, $code = null,$previous = null){
        $this->previousStepIndex = $previousStepIndex;
        $this->step = $step;
        $this->requestInfo = $requestInfo;
        $this->responseData = $responseData;
        if($code === null){
            $code = 0;
        }
        parent::__construct($message,$code,$previous);
    }

    /**
     * @return int|string
     */
    public function getPreviousStepIndex()
    {
        return $this->previousStepIndex;
    }

    /**
     * @return \paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface
     */
    public function getRequestInfo()
    {
        return $this->requestInfo;
    }

    /**
     * @return \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * @return SubmissionStepInterface
     */
    public function getStep()
    {
        return $this->step;
    }


}