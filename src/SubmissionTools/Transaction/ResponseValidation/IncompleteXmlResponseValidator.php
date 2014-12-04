<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class IncompleteXmlResponseValidator implements ResponseValidatorInterface{

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $response
     * @return boolean
     */
    public function isValid(ResponseDataInterface $response)
    {
        if($response->getException() !== null){
            return true; // some other error occured
        }

        $body = $response->getBody();
        // remove declaration
        $pattern = "#^\\s*<\\?xml[^>]*?\\?>\\s*#uis";
        $body = preg_replace($pattern,"",$body);
        // remove comments
        do{
            $pattern = "#^\\s*<!--.*?-->\\s*#uis";
            $count = 0;
            $body = preg_replace($pattern,"",$body,-1, $count);
        }while($count > 0);
        //get root element @todo make regex adhere to http://www.w3.org/TR/REC-xml/#NT-Name
        $patternRoot = "#^\\s*(<(?P<root1>[a-zA-Z:_][0-9a-zA-Z:_]*)>|<(?P<root2>[a-zA-Z:_][0-9a-zA-Z:_]*)[^>]*?[^/]>)\\s*#uis";
        if(preg_match($patternRoot,$body,$match)){
            $root = isset($match["root1"]) && $match["root1"] != ""?$match["root1"]:$match["root2"];
            $patternFull = "#<{$root}[^>]*?>.*</{$root}>#uis";
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