<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;

use paslandau\DomUtility\DomConverterInterface;
use paslandau\DomUtility\TidyWrapper;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

interface SimpleRequestInfoBuilderBuildInterface
{
    /**
     * @return RequestInfoInterface
     */
    public function build();
}

interface SimpleRequestInfoBuilderMethodInterface
{
    /**
     * @param string $method
     * @return SimpleRequestInfoBuilderUrlInterface
     */
    public function method($method);

    /**
     * @param string $url
     * @return SimpleRequestInfoBuilderConvertResponseToInterface
     */
    public function get($url);

    /**
     * @param string $url
     * @return SimpleRequestInfoBuilderConvertResponseToInterface
     */
    public function post($url);
}

interface SimpleRequestInfoBuilderUrlInterface
{
    /**
     * @param string $url
     * @return SimpleRequestInfoBuilderConvertResponseToInterface
     */
    public function url($url);
}

interface SimpleRequestInfoBuilderConvertResponseToInterface
{
    /**
     * @param string $encoding
     * @param null|bool $replaceHeaders
     * @param null|bool $replaceContent
     * @param null|string $fixedInputEncoding
     * @return SimpleRequestInfoBuilderConvertToDomInterface
     */
    public function convertResponseTo($encoding, $replaceHeaders = null, $replaceContent = null, $fixedInputEncoding = null);

    /**
     * @param EncodingConverterInterface $converter
     * @return SimpleRequestInfoBuilderConvertToDomInterface
     */
    public function convertResponseByConverter(EncodingConverterInterface $converter);

    /**
     * @return SimpleRequestInfoBuilderConvertToDomInterface
     */
    public function doNotConvertEncoding();
}

interface SimpleRequestInfoBuilderConvertToDomInterface
{
    /**
     * @param null|bool $setToInternalEncoding
     * @param TidyWrapper $tidy
     * @return SimpleRequestInfoBuilderOptionalsInterface
     */
    public function useHtmlDomConverter($setToInternalEncoding = null, TidyWrapper $tidy = null);

    /**
     * @param null|bool $setToInternalEncoding
     * @param TidyWrapper $tidy
     * @return SimpleRequestInfoBuilderOptionalsInterface
     */
    public function useXmlDomConverter($setToInternalEncoding = null, TidyWrapper $tidy = null);

    /**
     * @param DomConverterInterface $converter
     * @return SimpleRequestInfoBuilderOptionalsInterface
     */
    public function useDomComverter(DomConverterInterface $converter);

    /**
     * @return SimpleRequestInfoBuilderOptionalsInterface
     */
    public function doNotConvertDom();
}

//interface SimpleRequestInfoBuilderWithSessionInterface{
//    /**
//     * @param SessionInterface $session
//     * @return SimpleRequestInfoBuilderOptionalsInterface
//     */
//    public function withSession(SessionInterface $session);
//
//    /**
//     * @return SimpleRequestInfoBuilderOptionalsInterface
//     */
//    public function withoutSession();
//}

interface SimpleRequestInfoBuilderOptionalsInterface extends SimpleRequestInfoBuilderBuildInterface
{
    /**
     * @param array $payload
     * @return SimpleRequestInfoBuilderOptionalsInterface
     */
    public function payload(array $payload);

    /**
     * @param array $headers
     * @return SimpleRequestInfoBuilderOptionalsInterface
     */
    public function headers(array $headers);

    /**
     * @param array $options
     * @return SimpleRequestInfoBuilderOptionalsInterface
     */
    public function options(array $options);

    /**
     * @param ResponseValidatorInterface[] $responseValidators
     */
    /**
     * @param ResponseValidatorInterface[] $responseValidators
     * @param int $maxRetries [optional]. Default: null.
     * @return SimpleRequestInfoBuilderOptionalsInterface
     */
    public function validateResponseBy(array $responseValidators, $maxRetries = null);
}


interface SimpleRequestInfoBuilderBuildOrderInterface extends SimpleRequestInfoBuilderMethodInterface, SimpleRequestInfoBuilderUrlInterface, SimpleRequestInfoBuilderConvertResponseToInterface, SimpleRequestInfoBuilderConvertToDomInterface, SimpleRequestInfoBuilderOptionalsInterface
{
} 