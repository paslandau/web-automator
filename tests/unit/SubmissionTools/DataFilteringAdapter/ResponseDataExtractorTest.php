<?php

use paslandau\WebAutomator\SubmissionTools\DataFilteringAdapter\ResponseDataExtractor;

class ResponseDataExtractorTest extends PHPUnit_Framework_TestCase
{

    private $responseData;
    private $transformers;

    private $methods;

    public function setUp()
    {
        $this->methods = array(
            "GetBody",
            "GetStatusCode",
            "GetHeaders",
            "GetUrl",
            "GetVersion",
            "GetReasonPhrase",
        );
        $this->responseData = $this->getMock('paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface');
        $this->transformers = array(null);
        $t = $this->getMock('paslandau\DataFiltering\Transformation\DataTransformationInterface');
        $t->expects($this->any())->method("Transform")->will($this->returnArgument(0));
        $this->transformers[] = $t;
    }

    public function testTrue()
    {
        foreach ($this->transformers as $t) {
            foreach ($this->responseData as $method) {
                $r = new ResponseDataExtractor($method, $t);
                $actual = $r->GetData($this->responseData);
                $this->assertEquals(null, $actual);
            }
        }
    }

    public function testFalse()
    {
        foreach ($this->transformers as $t) {
            $tests = array(
                null,
                "Unknown method"
            );
            $this->setExpectedException(get_class(new InvalidArgumentException()));
            foreach ($tests as $method) {
                $r = new ResponseDataExtractor($method, $t);
            }
        }
    }
}
