<?php
namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData;

use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\RequestException;

interface ResponseDataInterface{

    /**
     * @return string
     */
    public function getBody();

    /**
     * Transforms the body of the response in a DomDocument.
     * @return \DOMDocument
     */
    public function getDomDocument();

    /**
     * @return string
     */
    public function getStatusCode();

    /**
     * @return mixed[]
     */
    public function getHeaders();

    /**
     * Last URL during the request
     * @return string
     */
    public function getUrl();

    /**
     * @return string
     */
    public function getReasonPhrase();

    /**
     * @return string
     */
    public function getProtocolVersion();

    /**
     * @return null|RequestException
     */
    public function getException();
}