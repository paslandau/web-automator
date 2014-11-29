<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class IncompleteJsonResponseValidator implements ResponseValidatorInterface{

    /**
     * @param ResponseDataInterface $response
     * @return boolean
     */
    public function isValid(ResponseDataInterface $response)
    {
        if($response->GetException() !== null){
            return true; // some other error occured
        }
        $body = $response->GetBody();
        $patternRoot = "#^\\s*(\\{|\\[)#uis";
        if(preg_match($patternRoot,$body)){
            if(!json_decode($body)){
                return false;
            }
        }
        return true;
    }

    /**
     * @param RequestInfoInterface $requestInfo
     */
    public function fixRequest(RequestInfoInterface &$requestInfo){

    }
}