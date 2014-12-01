<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo;


use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfigInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

class SimpleRequestInfo implements RequestInfoInterface
{

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var mixed
     */
    protected $payload;

    /**
     * @var mixed[]|null
     */
    protected $headers;

    /**
     * @var mixed[]|null
     */
    protected $options;

    /**
     * @var DomConverterInterface|null
     */
    protected $domConverter;

    /**
     * @var EncodingConverterInterface|null
     */
    protected $encodingConverter;

    /**
     * @var ResponseValidatorConfigInterface|null
     */
    protected $responseValidators;

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
     * @return mixed[]|null
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed[]|null $headers
     */
    public function setHeaders(array $headers = null)
    {
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return \mixed[]|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed[]|null $options
     */
    public function setOptions(array $options = null)
    {
        $this->options = $options;
    }

    /**
     * @return mixed|null
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed|null $payload
     */
    public function setPayload($payload = null)
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return ResponseValidatorConfigInterface|null
     */
    public function getResponseValidators()
    {
        return $this->responseValidators;
    }

    /**
     * @param ResponseValidatorConfigInterface|null $validators
     * @return void
     */
    public function setResponseValidators(ResponseValidatorConfigInterface $validators = null)
    {
        $this->responseValidators = $validators;
    }

    /**
     * @return DomConverterInterface|null
     */
    public function getDomConverter()
    {
        return $this->domConverter;
    }

    /**
     * @param DomConverterInterface $domConverter|null
     */
    public function setDomConverter($domConverter = null)
    {
        $this->domConverter = $domConverter;
    }

    /**
     * @return EncodingConverterInterface|null
     */
    public function getEncodingConverter()
    {
        return $this->encodingConverter;
    }

    /**
     * @param EncodingConverterInterface $encodingConverter|null
     */
    public function setEncodingConverter(EncodingConverterInterface $encodingConverter = null)
    {
        $this->encodingConverter = $encodingConverter;
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

    public function mergeWithDefaults(RequestInfoInterface $requestInfo = null)
    {
        if ($requestInfo === null) {
            return;
        }
        foreach ($this as $prop => $val) {
            if ($val === null) { // override null values in any case
                $this->{$prop} = $requestInfo->{$prop};
            }
            if ($requestInfo->{$prop} !== null && is_array($requestInfo->{$prop})) { // merge arrays
                if (is_array($val)) { // note: if $val was not set [= null] , it would have been overriden before
                    $this->{$prop} = array_replace_recursive($requestInfo->{$prop}, $val);
                }
            }
        }
    }
}