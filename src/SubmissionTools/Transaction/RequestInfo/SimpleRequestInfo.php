<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo;


use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfigInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

class SimpleRequestInfo extends AbstractBaseRequestInfo implements RequestInfoInterface
{
    /**
     * @var SessionInterface|null
     */
    protected $session;

    /**
     * @param string $method [optional]. Default: null.
     * @param string $url [optional]. Default: null.
     * @param $payload [optional]. Default: null.
     * @param $headers [optional]. Default: null.
     * @param $options [optional]. Default: null.
     * @param ResponseValidatorConfigInterface $responseValidators [optional]. Default: null.
     * @param DomConverterInterface $domConverter [optional]. Default: null.
     * @param EncodingConverterInterface $encodingConverter [optional]. Default: null.
     * @param SessionInterface $session [optional]. Default: null.
     */
    function __construct($method = null, $url = null, $payload = null, $headers = null, $options = null, ResponseValidatorConfigInterface $responseValidators = null, DomConverterInterface $domConverter = null, EncodingConverterInterface $encodingConverter = null, SessionInterface $session = null)
    {
        $this->method = $method;
        $this->url = $url;
        $this->payload = $payload;
        $this->headers = $headers;
        $this->options = $options;
        $this->domConverter = $domConverter;
        $this->responseValidators = $responseValidators;
        $this->encodingConverter = $encodingConverter;
        $this->session = $session;
    }

    /**
     * @return SessionInterface|null
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param SessionInterface|null $session
     */
    public function setSession($session = null)
    {
        $this->session = $session;
    }
}