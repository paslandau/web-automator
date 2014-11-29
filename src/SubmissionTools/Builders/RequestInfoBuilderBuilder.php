<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\ArrayUtility\ArrayPath\ArrayMergingStrategy;
use paslandau\ArrayUtility\ArrayPath\ArrayPath;
use paslandau\DomUtility\DomConverterInterface;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Extraction\InputDataExtractor;
use paslandau\DataFiltering\Extraction\MultiDataExtractor;
use paslandau\DataFiltering\Transformation\ArrayMergeTransformer;
use paslandau\DataFiltering\Transformation\ArrayMergingDescriptor;
use paslandau\DataFiltering\Transformation\ArraySelectSingleTransformer;
use paslandau\DataFiltering\Transformation\HtmlFormTransformer;
use paslandau\DataFiltering\Transformation\PropertyTransformer;
use paslandau\DataFiltering\Transformation\XpathTransformer;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoBuilder;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfig;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

/**
 * @method RequestInfoBuilderBuilder method($extractorOrInputData);
 * @method RequestInfoBuilderBuilder url($extractorOrInputData);
 * @method RequestInfoBuilderBuilder payload($extractorOrInputData);
 * @method RequestInfoBuilderBuilder options($extractorOrInputData);
 * @method RequestInfoBuilderBuilder headers($extractorOrInputData);
 */
class RequestInfoBuilderBuilder
{

    private $method;
    private $url;
    private $payload;
    private $options;
    private $headers;

    private $maxRetries;
    private $validators;
    private $domConverter;
    private $encodingConverter;

    private function __construct()
    {

    }

    public static function init()
    {
        return new self();
    }

    public function fillFromForm($xpath)
    {
        if (is_string($xpath)) {
            $domTrans = new XpathTransformer($xpath);
            $selectSingle = new ArraySelectSingleTransformer(0, false, $domTrans);
            $xpath = new HtmlFormTransformer($selectSingle);
        }
        $form = $xpath;
        $this->method['form'] = ExtractorBuilder::init()->useDomDocument()->postProcess(new PropertyTransformer("method", $form))->build();

        $url = new PropertyTransformer("action", $form);
        $urlEx = ExtractorBuilder::init()->useDomDocument()->postProcess($url)->build();
        $this->url['form'] = Build::extractorMulti()->absoluteUrl($urlEx)->build();
        // enctype? // mulipart form?
        $this->payload['form'] = ExtractorBuilder::init()->useDomDocument()->postProcess(new PropertyTransformer("fields", $form))->build();
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (!property_exists($this, $name)) {
            throw new \UnexpectedValueException("$name is unknown!");
        }
        $value = array_shift($arguments);
        if ($value instanceof DataExtractorInterface) {
            $this->{$name}['scraped'] = $value;
        } else {
            $this->{$name}['input'] = new InputDataExtractor($value);
        }
        return $this;
    }

    public function validators(array $validators, $maxRetries = null)
    {
        $this->validators = $validators;
        $this->maxRetries = $maxRetries;
        return $this;
    }

    public function domConverter(DomConverterInterface $domConverter)
    {
        $this->domConverter = $domConverter;
        return $this;
    }

    public function encodingConverter(EncodingConverterInterface $encodingConverter)
    {
        $this->encodingConverter = $encodingConverter;
        return $this;
    }

    public function build()
    {

        $check = [
            "method",
            "url",
            "payload",
            "options",
            "headers",
        ];
        $res = [];
        foreach ($check as $name) {
            $val = $this->{$name};
            if ($val === null || count($val) == 0) {
                $res[$name] = null;
            } elseif (count($val) == 1) {
                $res[$name] = array_shift($val);
            } else {
                $ds = [];
                foreach ($val as $type => $ex) {
                    $ds[] = new ArrayMergingDescriptor(new ArrayPath('["' . $type . '"]'), new ArrayPath(""), new ArrayMergingStrategy(ArrayMergingStrategy::STRATEGY_REPLACE));
                }
                $merger = new ArrayMergeTransformer($ds);
                $res[$name] = new MultiDataExtractor($val, $merger);
            }
        }
        $rvc = null;
        if ($this->validators !== null) {
            $rvc = new ResponseValidatorConfig($this->validators, $this->maxRetries);
        }
        return new RequestInfoBuilder($res["method"], $res["url"], $res["payload"], $res["headers"], $res["options"], $rvc, $this->domConverter, $this->encodingConverter);
    }
}