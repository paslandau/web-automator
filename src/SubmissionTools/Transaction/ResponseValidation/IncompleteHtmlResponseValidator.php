<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class IncompleteHtmlResponseValidator implements ResponseValidatorInterface{

    /**
     * @param ResponseDataInterface $response
     * @return boolean
     */
    public function isValid(ResponseDataInterface $response)
    {
        if($response->getException() !== null){
            return true; // some other error occured
        }
        $body = $response->getBody();
        $patternOpen = "#<html>|<html[^>]*?[^/]>#uis";
        $patternFull = "#<html[^>]*?>.*</html>#uis";
        if(preg_match($patternOpen,$body)){
            if(!preg_match($patternFull,$body)){
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