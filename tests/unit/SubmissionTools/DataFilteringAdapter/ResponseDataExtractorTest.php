<?php

use paslandau\DataFiltering\Transformation\DataTransformerInterface;
use paslandau\WebAutomator\SubmissionTools\DataFilteringAdapter\ResponseDataExtractor;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class ResponseDataExtractorTest extends PHPUnit_Framework_TestCase
{

    private $responseData;
    private $transformers;

    private $methods;

    public function setUp()
    {
        $this->methods = array(
            "getBody",
            "getStatusCode",
            "getHeaders",
            "getUrl",
            "getVersion",
            "getReasonPhrase",
        );
        $this->responseData = $this->getMock(ResponseDataInterface::class);
        $this->transformers = [
            null
        ];
        $t = $this->getMock(DataTransformerInterface::class);
        /** echo transformer */
        $t->expects($this->any())->method("transform")->will($this->returnArgument(0));
        $this->transformers[] = $t;
    }

    public function test_ShouldNotThrowExceptionOnKnownMethod()
    {
        foreach ($this->transformers as $t) {
            foreach ($this->responseData as $method) {
                $r = new ResponseDataExtractor($method, $t);
                $actual = $r->GetData($this->responseData);
                $this->assertEquals(null, $actual);
            }
        }
    }

    public function test_ShouldThrowExceptionOnUnknownMethod()
    {
        foreach ($this->transformers as $t) {
            $tests = [
                "null" => null,
                "Unknown method" => "Unknown method"
            ];
            foreach ($tests as $test => $method) {
                $exception = null;
                try {
                    new ResponseDataExtractor($method, $t);
                }catch(Exception $e){
                    $exception = get_class($e);
                }
                $this->assertEquals(InvalidArgumentException::class,$exception, "Test '$test' failed");
            }
        }
    }

    public function test_extract_ShouldThrowExceptionOnNull()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $r = new ResponseDataExtractor("GetBody");
        $r->getData(null);
    }
}
