<?php

use paslandau\ArrayUtility\ArrayUtil;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\GuzzleResponseData;

class GuzzleResponseDataTest extends PHPUnit_Framework_TestCase
{

    private $responseData;
    private $headers;
    private $body;
    private $version;
    private $statusCode;
    private $url;
    private $reasonPhrase;


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
        $this->responseData = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $this->headers = array(
            "location" => "http://www.example.com/"
        );
        $this->body = "body";
        $this->responseData->expects($this->any())->method('getHeaders')->will($this->returnValue($this->headers));
        $streamMock = $this->getMock('GuzzleHttp\Stream\StreamInterface');
        $streamMock->expects($this->any())->method('__toString')->will($this->returnValue($this->body));
        $this->responseData->expects($this->any())->method('getBody')->will($this->returnValue($streamMock));
        $this->version = "1.1";
        $this->responseData->expects($this->any())->method('getProtocolVersion')->will($this->returnValue($this->version));
        $this->statusCode = "301";
        $this->responseData->expects($this->any())->method('getStatusCode')->will($this->returnValue($this->statusCode));
        $this->url = "http://www.example.org/";
        $this->responseData->expects($this->any())->method('getEffectiveUrl')->will($this->returnValue($this->url));
        $this->reasonPhrase = "Moved Permanently";
        $this->responseData->expects($this->any())->method('getReasonPhrase')->will($this->returnValue($this->reasonPhrase));
    }

    public function testTrue()
    {
        $r = new GuzzleResponseData($this->responseData);
        $this->assertEquals($this->version, $r->GetProtocolVersion());
        $this->assertEquals($this->body, $r->GetBody());
        $this->assertEquals($this->statusCode, $r->GetStatusCode());
        $this->assertEquals($this->url, $r->GetUrl());
        $this->assertEquals($this->reasonPhrase, $r->GetReasonPhrase());
        $h = $r->GetHeaders();
        $res = ArrayUtil::containsArray($this->headers, $h, true, false, true);
        $msg = json_encode($h);
        $this->assertTrue($res, $msg);
    }

    public function testFalse()
    {
        $r = new GuzzleResponseData($this->responseData);
        $this->assertNotEquals($this->version . " wrong", $r->GetProtocolVersion());
        $this->assertNotEquals($this->body . " wrong", $r->GetBody());
        $this->assertNotEquals($this->statusCode . " wrong", $r->GetStatusCode());
        $this->assertNotEquals($this->url . " wrong", $r->GetUrl());
        $this->assertNotEquals($this->reasonPhrase . " wrong", $r->GetReasonPhrase());
        $out = null;
        $wrongHeaders = ["wrong" => "headers"];
        $res = ArrayUtil::containsArray($wrongHeaders, $r->GetHeaders(), true, false, true);
        $msg = json_encode($r->GetHeaders());
        $this->assertFalse($res, $msg);
    }
}
