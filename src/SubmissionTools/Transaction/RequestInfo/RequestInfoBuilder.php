<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo;


use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfigInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

class RequestInfoBuilder implements RequestInfoBuilderInterface
{

    /**
     * @var EncodingConverterInterface
     */
    protected $encodingConverter;
    /**
     * @var ResponseValidatorConfigInterface
     */
    protected $responseValidators;
    /**
     * @var DataExtractorInterface
     */

    private $methodExtractor;
    /**
     * @var DataExtractorInterface
     */
    private $urlExtractor;
    /**
     * @var DataExtractorInterface
     */
    private $payloadExtractor;
    /**
     * @var DataExtractorInterface
     */
    private $headerExtractor;
    /**
     * @var DomConverterInterface
     */
    private $domConverter;
    /**
     * @var DataExtractorInterface
     */
    private $optionsExtractor;

    /**
     * @param DataExtractorInterface $methodExtractor
     * @param DataExtractorInterface $urlExtractor
     * @param DataExtractorInterface $payloadExtractor [optional]. Default: null.
     * @param DataExtractorInterface $headerExtractor [optional]. Default: null.
     * @param DataExtractorInterface $optionsExtractor [optional]. Default: null.
     * @param ResponseValidatorConfigInterface $responseValidators [optional]. Default: null.
     * @param DomConverterInterface $domConverter [optional]. Default: null.
     * @param EncodingConverterInterface $encodingConverter [optional]. Default: null.
     */
    function __construct(DataExtractorInterface $methodExtractor, DataExtractorInterface $urlExtractor, DataExtractorInterface $payloadExtractor = null, DataExtractorInterface $headerExtractor = null, DataExtractorInterface $optionsExtractor = null, ResponseValidatorConfigInterface $responseValidators = null, DomConverterInterface $domConverter = null, EncodingConverterInterface $encodingConverter = null)
    {
        $this->methodExtractor = $methodExtractor;
        $this->urlExtractor = $urlExtractor;
        $this->payloadExtractor = $payloadExtractor;
        $this->headerExtractor = $headerExtractor;
        $this->optionsExtractor = $optionsExtractor;
        $this->domConverter = $domConverter;
        $this->responseValidators = $responseValidators;
        $this->encodingConverter = $encodingConverter;
    }

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return SimpleRequestInfo
     */
    public function buildRequestInfo(ResponseDataInterface $responseData)
    {
        $method = $this->methodExtractor->GetData($responseData);
        $url = $this->urlExtractor->GetData($responseData);
        //todo: what about GET / POST payload differentiation?
        $payload = $this->payloadExtractor === null ? array() : $this->payloadExtractor->GetData($responseData);
        $headers = $this->headerExtractor === null ? array() : $this->headerExtractor->GetData($responseData);
        $options = $this->optionsExtractor === null ? array() : $this->optionsExtractor->GetData($responseData);

        $requestInfo = new SimpleRequestInfo($method, $url, $payload, $headers, $options, $this->responseValidators, $this->domConverter);
        return $requestInfo;
    }

    /**
     * @return \paslandau\DataFiltering\Extraction\DataExtractorInterface
     */
    public function getHeaderExtractor()
    {
        return $this->headerExtractor;
    }

    /**
     * @param \paslandau\DataFiltering\Extraction\DataExtractorInterface $headerExtractor
     */
    public function setHeaderExtractor($headerExtractor)
    {
        $this->headerExtractor = $headerExtractor;
    }

    /**
     * @return \paslandau\DataFiltering\Extraction\DataExtractorInterface
     */
    public function getMethodExtractor()
    {
        return $this->methodExtractor;
    }

    /**
     * @param \paslandau\DataFiltering\Extraction\DataExtractorInterface $methodExtractor
     */
    public function setMethodExtractor($methodExtractor)
    {
        $this->methodExtractor = $methodExtractor;
    }

    /**
     * @return \paslandau\DataFiltering\Extraction\DataExtractorInterface
     */
    public function getOptionsExtractor()
    {
        return $this->optionsExtractor;
    }

    /**
     * @param \paslandau\DataFiltering\Extraction\DataExtractorInterface $optionsExtractor
     */
    public function setOptionsExtractor($optionsExtractor)
    {
        $this->optionsExtractor = $optionsExtractor;
    }

    /**
     * @return \paslandau\DataFiltering\Extraction\DataExtractorInterface
     */
    public function getPayloadExtractor()
    {
        return $this->payloadExtractor;
    }

    /**
     * @param \paslandau\DataFiltering\Extraction\DataExtractorInterface $payloadExtractor
     */
    public function setPayloadExtractor($payloadExtractor)
    {
        $this->payloadExtractor = $payloadExtractor;
    }

    /**
     * @return \paslandau\DataFiltering\Extraction\DataExtractorInterface
     */
    public function getUrlExtractor()
    {
        return $this->urlExtractor;
    }

    /**
     * @param \paslandau\DataFiltering\Extraction\DataExtractorInterface $urlExtractor
     */
    public function setUrlExtractor($urlExtractor)
    {
        $this->urlExtractor = $urlExtractor;
    }

    /**
     * @return ResponseValidatorConfigInterface
     */
    public function getResponseValidators()
    {
        return $this->responseValidators;
    }

    /**
     * @param ResponseValidatorConfigInterface $validators
     * @return void
     */
    public function setResponseValidators(ResponseValidatorConfigInterface $validators)
    {
        $this->responseValidators = $validators;
    }

    /**
     * @return DomConverterInterface
     */
    public function getDomConverter()
    {
        return $this->domConverter;
    }

    /**
     * @param DomConverterInterface $domConverter
     */
    public function setDomConverter($domConverter)
    {
        $this->domConverter = $domConverter;
    }

    /**
     * @return EncodingConverterInterface
     */
    public function getEncodingConverter()
    {
        return $this->encodingConverter;
    }

    /**
     * @param EncodingConverterInterface $encodingConverter
     */
    public function setEncodingConverter($encodingConverter)
    {
        $this->encodingConverter = $encodingConverter;
    }
}