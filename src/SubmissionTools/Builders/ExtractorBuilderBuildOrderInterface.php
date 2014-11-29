<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;

use paslandau\DataFiltering\Transformation\BooleanTransformerInterface;
use paslandau\DataFiltering\Transformation\DataTransformerInterface;
use paslandau\DataFiltering\Transformation\DomDocumentTransformerInterface;
use paslandau\DataFiltering\Transformation\DomNodeToStringTransformer;
use paslandau\DataFiltering\Transformation\StringTransformerInterface;
use paslandau\WebAutomator\SubmissionTools\DataFilteringAdapter\ResponseDataExtractor;

interface ExtractorBuild
{
    /**
     * @return ResponseDataExtractor
     */
    public function build();
}

interface ExtratcorPostProcess
{
    /**
     * @param DataTransformerInterface $trans
     * @return ExtractorAnything
     */
    public function postProcess(DataTransformerInterface $trans);

    /**
     * @param StringTransformerInterface $trans
     * @return ExtractorStringTransformer
     */
    public function postProcessToString(StringTransformerInterface $trans);

    /**
     * @param BooleanTransformerInterface $trans
     * @return ExtractorBooleanTransformer
     */
    public function postProcessToBoolean(BooleanTransformerInterface $trans);

    /**
     * @param DomDocumentTransformerInterface $trans
     * @return ExtractorDomDocumentTransformer
     */
    public function postProcessToDomDocument(DomDocumentTransformerInterface $trans);
}

interface ExtractorBooleanTransformer extends ExtractorBuild, ExtratcorPostProcess
{

}

interface ExtractorStringTransformer extends ExtractorBuild, ExtratcorPostProcess
{
    /**
     * @param $pattern
     * @param null $groupIndex
     * @param int $selectIndex
     * @param bool $canBeNull
     * @return ExtractorStringTransformer
     */
    public function regex($pattern, $groupIndex = null, $selectIndex = 0, $canBeNull = false);

    /**
     * @param string $pattern
     * @param int[]|string[] $groupIndex [optional]. Default: null. (select group 0 => full pattern match)
     * @param array $selectIndices [optional]. Default: null. (select all)
     * @param bool $canBeNull [optional]. Default: false. (throw error on null)
     * @return ExtractorArrayTransformer
     */
    public function regexArray($pattern, $groupIndex = null, array $selectIndices = null, $canBeNull = false);

    /**
     * @param $pattern
     * @param null $groupIndex
     * @param bool $canBeNull
     * @return ExtractorBooleanTransformer
     */
    public function regexExists($pattern, $groupIndex = null, $canBeNull = false);
}

interface ExtractorDomDocumentTransformer extends ExtractorBuild, ExtratcorPostProcess
{
    /**
     * @param $xpathToForm
     * @param int $index
     * @param bool $canBeNull
     * @return ExtractorAnything
     */
    public function htmlForm($xpathToForm, $index = 0, $canBeNull = false);

    /**
     * @param $xpath
     * @param int $selectIndex
     * @param bool $canBeNull
     * @return ExtractorDomDocumentTransformer
     */
    public function xpath($xpath, $selectIndex = 0, $canBeNull = false);

    /**
     * @param $xpath
     * @param string $domToStringMethod
     * @param int $selectIndex
     * @param bool $canBeNull
     * @return ExtractorStringTransformer
     */
    public function xpathString($xpath, $domToStringMethod = DomNodeToStringTransformer::METHOD_NODE_VALUE, $selectIndex = 0, $canBeNull = false);

    /**
     * @param $xpath
     * @param bool $canBeNull
     * @return ExtractorBooleanTransformer
     */
    public function xpathExists($xpath, $canBeNull = false);
}

interface ExtractorArrayTransformer extends ExtractorBuild
{
    /**
     * @param mixed $index
     * @param null $canBeNull
     * @return ExtractorAnything
     */
    public function selectIndex($index, $canBeNull = null);

    /**
     * @param array $indexes
     * @param null $canBeNull
     * @return ExtractorArrayTransformer
     */
    public function selectIndexArray(array $indexes, $canBeNull = null);
}

interface ExtractorTypeTransformer extends ExtractorBooleanTransformer, ExtractorStringTransformer, ExtractorDomDocumentTransformer, ExtractorArrayTransformer
{

}

interface ExtractorAnything extends ExtractorTypeTransformer
{

}

interface ExtractorUseMethod
{
    /**
     * @return ExtractorStringTransformer
     */
    public function useBody();

    /**
     * @return ExtractorStringTransformer
     */
    public function useUrl();

    /**
     * @return ExtractorDomDocumentTransformer
     */
    public function useDomDocument();

    /**
     * @return ExtractorStringTransformer
     */
    public function useStatusCode();

    /**
     * @return ExtractorStringTransformer
     */
    public function useProtocolVersion();

    /**
     * @return ExtractorArrayTransformer
     */
    public function useHeaders();

    /**
     * @return ExtractorStringTransformer
     */
    public function useReasonPhrase();
}

interface ExtractorBuilderBuildOrderInterface extends ExtractorAnything, ExtractorUseMethod
{

} 