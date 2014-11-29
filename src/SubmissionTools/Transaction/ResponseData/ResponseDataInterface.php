<?php
namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData;

use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\RequestException;

interface ResponseDataInterface{

    /**
     * @return string
     */
    public function GetBody();

    /**
     * Transforms the body of the response in a DomDocument.
     * @return \DOMDocument
     */
    public function GetDomDocument();

    /**
     * @return string
     */
    public function GetStatusCode();

    /**
     * @return mixed[]
     */
    public function GetHeaders();

    /**
     * Last URL during the request
     * @return string
     */
    public function GetUrl();

    /**
     * @return string
     */
    public function GetReasonPhrase();

    /**
     * @return string
     */
    public function GetProtocolVersion();

    /**
     * @return null|RequestException
     */
    public function GetException();
}