<?php

use paslandau\ComparisonUtility\ComparisonObject;
use paslandau\ComparisonUtility\ComparisonObjectInterface;
use paslandau\ComparisonUtility\StringComperator;
use paslandau\DataFiltering\Transformation\ArraySelectSingleTransformer;
use paslandau\DataFiltering\Transformation\DomDocumentTransformer;
use paslandau\DataFiltering\Transformation\DomNodeToStringTransformer;
use paslandau\DataFiltering\Transformation\XpathTransformer;
use paslandau\DataFiltering\Evaluation\DataEvaluator;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DomUtility\DomConverter;
use paslandau\IOUtility\IOUtil;
use paslandau\WebAutomator\SubmissionTools\DataFilteringAdapter\ResponseDataExtractor;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverter;

class ResponseDataEvaluatorTest extends PHPUnit_Framework_TestCase
{

    private $html5;

    /**
     * @var DataExtractorInterface
     */
    private $extractorMock;

    public function setUp()
    {
        $path = __DIR__ . "/../../resources/html5.html";
        $this->html5 = IOUtil::getFileContent($path);
        $this->extractorMock = $this->getMock(DataExtractorInterface::class);

        // Returns the body

        $callback = function (ResponseDataInterface $response) {
            return $response->getBody();
        };
        $this->extractorMock->expects($this->any())->method('getData')->will($this->returnCallback($callback));
    }

    /**
     * @param $expected
     * @return ComparisonObjectInterface
     */
    private function GetComparisonMock($expected)
    {
        // Compares given data to $expected
        $comperatorMock = $this->getMock(ComparisonObjectInterface::class);
        $callback = function ($actual) use ($expected) {
            return $actual == $expected;
        };
        $comperatorMock->expects($this->any())->method('compareToExpected')->will($this->returnCallback($callback));
        return $comperatorMock;
    }

    /**
     * @param $returnValue
     * @return ResponseDataInterface
     */
    private function GetResponseMock($returnValue)
    {
        $responseMock = $this->getMock(ResponseDataInterface::class);
        $responseMock->expects($this->any())->method('getBody')->will($this->returnValue($returnValue));
        return $responseMock;
    }

    public function testTrue()
    {
        $expected = "Straßenschäden";
        $returnValue = $expected;
        $comparisonMock = $this->GetComparisonMock($expected);
        $obj = new DataEvaluator($comparisonMock, $this->extractorMock);
        $responseMock = $this->GetResponseMock($returnValue);
        $result = $obj->solve($responseMock);
        $this->assertTrue($result);
    }

    public function testFalse()
    {
        $expected = "Straßenschäden";
        $returnValue = "Straßenschaden";;
        $comparisonMock = $this->GetComparisonMock($expected);
        $obj = new DataEvaluator($comparisonMock, $this->extractorMock);
        $responseMock = $this->GetResponseMock($returnValue);
        $result = $obj->solve($responseMock);
        $this->assertFalse($result);
    }

    public function testHtmlTrue()
    {
        $expected = "baz";
        $returnValue = $this->html5;
        $xPathExpression = "//li";

        $comperator = new StringComperator(StringComperator::COMPARE_FUNCTION_EQUALS, false, false);
        $comparisonObject = new ComparisonObject($comperator, $expected);

        $converter = new EncodingConverter(mb_internal_encoding(), true, true);
        $domConverter = new DomConverter(DomConverter::HTML, $converter);
        $domTransformer = new DomDocumentTransformer($domConverter);
        $xpath = new XpathTransformer($xPathExpression, $domTransformer);
        $arrTrans = new ArraySelectSingleTransformer(2, false, $xpath);
        $dts = new DomNodeToStringTransformer(DomNodeToStringTransformer::METHOD_NODE_VALUE, $arrTrans);
        $extractor = new ResponseDataExtractor("getBody", $dts);

        $obj = new DataEvaluator($comparisonObject, $extractor);

        $responseMock = $this->GetResponseMock($returnValue);
        $result = $obj->solve($responseMock);
        $this->assertTrue($result);
    }

    public function testHtmlFalse()
    {
        $expected = "bar";
        $returnValue = $this->html5;
        $xPathExpression = "//li";

        $comperator = new StringComperator(StringComperator::COMPARE_FUNCTION_EQUALS, false, false);
        $comparisonObject = new ComparisonObject($comperator, $expected);

        $converter = new EncodingConverter(mb_internal_encoding(), true, true);
        $domConverter = new DomConverter(DomConverter::HTML, $converter);
        $domTransformer = new DomDocumentTransformer($domConverter);
        $xpath = new XpathTransformer($xPathExpression, $domTransformer);
        $arrTrans = new ArraySelectSingleTransformer(2, false, $xpath);
        $dts = new DomNodeToStringTransformer(DomNodeToStringTransformer::METHOD_NODE_VALUE, $arrTrans);
        $extractor = new ResponseDataExtractor("getBody", $dts);

        $obj = new DataEvaluator($comparisonObject, $extractor);

        $responseMock = $this->GetResponseMock($returnValue);
        $result = $obj->solve($responseMock);
        $this->assertFalse($result);
    }
}
