<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\DataFiltering\Transformation\ArrayLoopTransformer;
use paslandau\DataFiltering\Transformation\ArraySelectionTransformer;
use paslandau\DataFiltering\Transformation\ArraySelectSingleTransformer;
use paslandau\DataFiltering\Transformation\ArrayTransformerInterface;
use paslandau\DataFiltering\Transformation\DomNodeToStringTransformer;
use paslandau\DataFiltering\Transformation\HtmlFormTransformer;
use paslandau\DataFiltering\Transformation\RegexExistsTransformer;
use paslandau\DataFiltering\Transformation\RegexTransformer;
use paslandau\DataFiltering\Transformation\XpathBaseTransformer;
use paslandau\DataFiltering\Transformation\XpathExistsTransformer;
use paslandau\WebAutomator\SubmissionTools\DataFilteringAdapter\ResponseDataExtractor;

class ExtractorBuilder extends AbstractBaseExtractorBuilder implements ExtractorBuilderBuildOrderInterface
{

    /**
     * @var string
     */
    private $method;

    private function __construct()
    {
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public static function init()
    {
        return new self();
    }

    public function useBody(){
        $this->method = "getBody";
        return $this;
    }
    public function useUrl(){
        $this->method = "getUrl";
        return $this;
    }
    public function useDomDocument(){
        $this->method = "getDomDocument";
        return $this;
    }
    public function useStatusCode(){
        $this->method = "getStatusCode";
        return $this;
    }
    public function useProtocolVersion(){
        $this->method = "getProtocolVersion";
        return $this;
    }
    public function useHeaders(){
        $this->method = "getHeaders";
        return $this;
    }
    public function useReasonPhrase(){
        $this->method = "getReasonPhrase";
        return $this;
    }

    public function htmlForm($xpathToForm, $index = 0, $canBeNull = false)
    {
        $this->xpath($xpathToForm, $index, $canBeNull);
        $trans = new HtmlFormTransformer($this->trans, $canBeNull);
        $this->postProcess($trans);
        return $this;
    }

    public function xpath($xpath, $selectIndex = 0, $canBeNull = false)
    {
        $trans = new XpathBaseTransformer($xpath, null, $canBeNull);
        $trans = $this->select($trans, $selectIndex, $canBeNull);
        $this->postProcess($trans);
        return $this;
    }

    private function select(ArrayTransformerInterface $trans, $index = null, $canBeNull = null)
    {
        if (is_array($index)) {
            $trans = new ArraySelectionTransformer($index, $canBeNull, $trans, $canBeNull);
        } elseif ($index !== null) {
            $trans = new ArraySelectSingleTransformer($index, $canBeNull, $trans, $canBeNull);
        }
        return $trans;
    }

    public function selectIndex($index, $canBeNull = null){
        $trans = new ArraySelectSingleTransformer($index, $canBeNull, null, $canBeNull);
        $this->postProcess($trans);
        return $this;
    }

    public function selectIndexArray(array $indexes, $canBeNull = null){
        $trans = new ArraySelectionTransformer($indexes, $canBeNull, null, $canBeNull);
        $this->postProcess($trans);
        return $this;
    }

    public function xpathString($xpath, $domToStringMethod = DomNodeToStringTransformer::METHOD_NODE_VALUE, $selectIndex = 0, $canBeNull = false)
    {
        $this->xpath($xpath, $selectIndex, $canBeNull);
        $trans = new DomNodeToStringTransformer($domToStringMethod, null, $canBeNull);
        if ($selectIndex === null || is_array($selectIndex)) {
            $trans = new ArrayLoopTransformer($trans, null, $canBeNull); // predecessor will be set in next line:  $this->postProcess($trans);
        }
        $this->postProcess($trans);
        return $this;
    }

    public function xpathExists($xpath, $canBeNull = false)
    {
        $trans = new XpathExistsTransformer($xpath, null, $canBeNull);
        $this->postProcess($trans);
        return $this;
    }

    public function regexArray($pattern, $groupIndex = null, array $selectIndices = null, $canBeNull = false){
        return $this->regex($pattern, $groupIndex, $selectIndices, $canBeNull);
    }

    public function regex($pattern, $groupIndex = null, $selectIndex = 0, $canBeNull = false)
    {
        $trans = new RegexTransformer($pattern, $groupIndex, null, $canBeNull);
        $trans = $this->select($trans, $selectIndex, $canBeNull);
        $this->postProcess($trans);
        return $this;
    }

    public function regexExists($pattern, $groupIndex = null, $canBeNull = false)
    {
        $trans = new RegexExistsTransformer($pattern, $groupIndex, null, $canBeNull);
        $this->postProcess($trans);
        return $this;
    }

    /**
     * @return ResponseDataExtractor
     */
    public function build()
    {
        $this->base = new ResponseDataExtractor($this->method);
        return parent::build();
    }

}