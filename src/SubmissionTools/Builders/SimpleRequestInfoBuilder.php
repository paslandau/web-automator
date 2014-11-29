<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\DomUtility\DomConverter;
use paslandau\DomUtility\DomConverterInterface;
use paslandau\DomUtility\TidyWrapper;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\SimpleRequestInfo;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfig;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverter;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

class SimpleRequestInfoBuilder implements SimpleRequestInfoBuilderBuildOrderInterface, DefaultRequestInfoBuilderBuildOrderInterface
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
     * @var mixed[]
     */
    protected $headers;

    /**
     * @var mixed[]
     */
    protected $options;

    /**
     * @var DomConverterInterface
     */
    protected $domConverter;

    /**
     * @var EncodingConverterInterface
     */
    protected $encodingConverter;

    /**
     * @var ResponseValidatorInterface[]
     */
    protected $responseValidators;
    /**
     * @var int
     */
    protected $maxRetries;

    /**
     * @var SessionInterface
     */
    protected $session;

    private function __construct()
    {
    }

    public static function init()
    {
        return new self();
    }

    public function method($method)
    {
        $this->method = $method;
        return $this;
    }

    public function get($url)
    {
        $this->method("GET");
        return $this->url($url);
    }

    public function post($url)
    {
        $this->method("POST");
        return $this->url($url);
    }

    public function url($url)
    {
        $this->url = $url;
        return $this;
    }

    public function payload(array $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    public function headers(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function options(array $options)
    {
        $this->options = $options;
        return $this;
    }

    public function validateResponseBy(array $responseValidators, $maxRetries = null)
    {
        $this->responseValidators = $responseValidators;
        $this->maxRetries = $maxRetries;
        return $this;
    }

    public function withoutValidation()
    {
        return $this;
    }

    public function convertResponseTo($encoding, $replaceHeaders = null, $replaceContent = null, $fixedInputEncoding = null)
    {
        return $this->convertResponseByConverter(new EncodingConverter($encoding, $replaceHeaders, $replaceContent, $fixedInputEncoding));
    }

    public function convertResponseByConverter(EncodingConverterInterface $converter)
    {
        $this->encodingConverter = $converter;
        return $this;
    }

    public function doNotConvertEncoding()
    {
        return $this;
    }

    public function useHtmlDomConverter($setToInternalEncoding = null, TidyWrapper $tidy = null)
    {
        $converter = null;
        if ($setToInternalEncoding) {
            $converter = new EncodingConverter(mb_internal_encoding(), true, true);
        } else {
            $tidy = null;
        }
        return $this->useDomComverter(new DomConverter(DomConverter::HTML, $converter, $tidy));
    }

    public function useXmlDomConverter($setToInternalEncoding = null, TidyWrapper $tidy = null)
    {
        $converter = null;
        if ($setToInternalEncoding) {
            $converter = new EncodingConverter(mb_internal_encoding(), true, true);
        } else {
            $tidy = null;
        }
        return $this->useDomComverter(new DomConverter(DomConverter::XML, $converter, $tidy));
    }

    public function useDomComverter(DomConverterInterface $converter)
    {
        $this->domConverter = $converter;
        return $this;
    }

    public function doNotConvertDom()
    {
        return $this;
    }

    public function withSession(SessionInterface $session)
    {
        $this->session = $session;
        return $this;
    }

    public function withoutSession()
    {
        return $this;
    }

    public function build()
    {
        $rvc = null;
        if ($this->responseValidators !== null) {
            $rvc = new ResponseValidatorConfig($this->responseValidators, $this->maxRetries);
        }
        $s = new SimpleRequestInfo($this->method, $this->url, $this->payload, $this->headers, $this->options, $rvc, $this->domConverter, $this->encodingConverter, $this->session);
        return $s;
    }
}