<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation;


use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\CompressionException;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class CompressionErrorValidator implements ResponseValidatorInterface
{

    /**
     * @param ResponseDataInterface $response
     * @return boolean
     */
    public function isValid(ResponseDataInterface $response)
    {
        $e = $response->GetException();
        if ($e instanceof CompressionException) {
            return false;
        }
        return true;
    }

    /**
     * @param RequestInfoInterface $requestInfo
     */
    public function fixRequest(RequestInfoInterface &$requestInfo)
    {
        // remove compression info
        $options = $requestInfo->getOptions();
//        $err = fopen('php://output', 'w');
//        $err = STDERR;
//        $config = ["curl" => [CURLOPT_ENCODING => "gzip", CURLOPT_VERBOSE => 1, CURLOPT_STDERR => $err]];
////$config = ["curl" => [CURLOPT_STDERR => fopen('php://stdout', 'w')]];
//        $headers = ["Accept-Encoding" => "gzip"];
////$headers = [];
//        $decode = false;
        $options["decode"] = false;
        $requestInfo->setOptions($options);
        $headers = $requestInfo->getHeaders();
        foreach ($headers as $key => $val) {
            if (mb_strtolower($key) == mb_strtolower("Accept-Encoding")) {
                unset($headers[$key]);
            }
        }
        $requestInfo->setHeaders($headers);
    }
}