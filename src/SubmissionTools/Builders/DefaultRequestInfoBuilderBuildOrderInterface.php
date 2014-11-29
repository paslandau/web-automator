<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\DomUtility\DomConverterInterface;
use paslandau\DomUtility\TidyWrapper;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

interface DefaultRequestInfoBuilderBuildInterface
{
    /**
     * @return RequestInfoInterface
     */
    public function build();
}

interface DefaultRequestInfoBuilderConvertResponseToInterface
{
    /**
     * @param string $encoding
     * @param null|bool $replaceHeaders
     * @param null|bool $replaceContent
     * @param null|string $fixedInputEncoding
     * @return DefaultRequestInfoBuilderConvertToDomInterface
     */
    public function convertResponseTo($encoding, $replaceHeaders = null, $replaceContent = null, $fixedInputEncoding = null);

    /**
     * @param EncodingConverterInterface $converter
     * @return DefaultRequestInfoBuilderConvertToDomInterface
     */
    public function convertResponseByConverter(EncodingConverterInterface $converter);

    /**
     * @return DefaultRequestInfoBuilderConvertToDomInterface
     */
    public function doNotConvertEncoding();
}

interface DefaultRequestInfoBuilderConvertToDomInterface
{
    /**
     * @param null|bool $setToInternalEncoding
     * @param TidyWrapper $tidy
     * @return DefaultRequestInfoBuilderWithValidateByInterface
     */
    public function useHtmlDomConverter($setToInternalEncoding = null, TidyWrapper $tidy = null);

    /**
     * @param null|bool $setToInternalEncoding
     * @param TidyWrapper $tidy
     * @return DefaultRequestInfoBuilderWithValidateByInterface
     */
    public function useXmlDomConverter($setToInternalEncoding = null, TidyWrapper $tidy = null);

    /**
     * @param DomConverterInterface $converter
     * @return DefaultRequestInfoBuilderWithValidateByInterface
     */
    public function useDomComverter(DomConverterInterface $converter);

    /**
     * @return DefaultRequestInfoBuilderWithValidateByInterface
     */
    public function doNotConvertDom();
}

interface DefaultRequestInfoBuilderWithValidateByInterface
{
    /**
     * @param ResponseValidatorInterface[] $responseValidators
     * @param int $maxRetries [optional]. Default: null.
     * @return DefaultRequestInfoBuilderWithSessionInterface
     */
    public function validateResponseBy(array $responseValidators, $maxRetries = null);

    /**
     * @return DefaultRequestInfoBuilderWithSessionInterface
     */
    public function withoutValidation();
}

interface DefaultRequestInfoBuilderWithSessionInterface
{
    /**
     * @param SessionInterface $session
     * @return DefaultRequestInfoBuilderOptionalsInterface
     */
    public function withSession(SessionInterface $session);

    /**
     * @return DefaultRequestInfoBuilderOptionalsInterface
     */
    public function withoutSession();
}


interface DefaultRequestInfoBuilderOptionalsInterface extends DefaultRequestInfoBuilderBuildInterface
{
    /**
     * @param array $payload
     * @return DefaultRequestInfoBuilderOptionalsInterface
     */
    public function payload(array $payload);

    /**
     * @param array $headers
     * @return DefaultRequestInfoBuilderOptionalsInterface
     */
    public function headers(array $headers);

    /**
     * @param array $options
     * @return DefaultRequestInfoBuilderOptionalsInterface
     */
    public function options(array $options);
}


interface DefaultRequestInfoBuilderBuildOrderInterface extends DefaultRequestInfoBuilderConvertResponseToInterface, DefaultRequestInfoBuilderConvertToDomInterface, DefaultRequestInfoBuilderWithValidateByInterface, DefaultRequestInfoBuilderWithSessionInterface, DefaultRequestInfoBuilderOptionalsInterface
{
} 