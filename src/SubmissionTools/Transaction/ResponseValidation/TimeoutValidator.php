<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation;


use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\TimeoutException;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class TimeoutValidator implements ResponseValidatorInterface{

    /**
     * @param ResponseDataInterface $response
     * @return boolean
     */
    public function isValid(ResponseDataInterface $response)
    {
        $e = $response->GetException();
        if($e instanceof TimeoutException){
                return false;
        }
        return true;
    }

    /**
     * @param RequestInfoInterface $requestInfo
     */
    public function fixRequest(RequestInfoInterface &$requestInfo){

    }
}