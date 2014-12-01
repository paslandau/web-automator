<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo;

use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfigInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

/**
 * Interface RequestInfoInterface
 * @package paslandau\WebAutomator\SubmissionTools\Transaction\SimpleRequestInfo
 * @todo Define clearer interface, decoupled from Guzzle
 */
interface RequestInfoInterface
{

    /**
     * @param RequestInfoInterface $requestInfo
     */
    public function mergeWithDefaults(RequestInfoInterface $requestInfo);

    /**
     * HTTP Method
     * @return string
     */
    public function getMethod();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return mixed
     */
    public function getPayload();

    /**
     * @return mixed[]
     */
    public function getHeaders();

    /**
     * Request options for next GuzzleRequest
     * @return mixed[]
     */
    public function getOptions();

    /**
     * @return ResponseValidatorConfigInterface|null
     */
    public function getResponseValidators();

    /**
     * @return SessionInterface|null
     */
    public function getSession();

    /**
     * @return DomConverterInterface|null
     */
    public function getDomConverter();

    /**
     * @return EncodingConverterInterface|null
     */
    public function getEncodingConverter();

    /**
     * HTTP Method
     * @param string $method
     */
    public function setMethod($method);

    /**
     * @param string $url
     */
    public function setUrl($url);

    /**
     * @param mixed|null $payload
     */
    public function setPayload($payload = null);

    /**
     * @param mixed[]|null $headers
     */
    public function setHeaders(array $headers = null);

    /**
     * Request options for next GuzzleRequest
     * @param mixed[]|null $options
     */
    public function setOptions(array $options = null);

    /**
     * @param ResponseValidatorConfigInterface|null $validators
     * @return void
     */
    public function setResponseValidators(ResponseValidatorConfigInterface $validators = null);

    /**
     * @param DomConverterInterface|null $domConverter
     */
    public function setDomConverter($domConverter = null);

    /**
     * @param EncodingConverterInterface|null $encodingConverter
     */
    public function setEncodingConverter(EncodingConverterInterface $encodingConverter = null);
}