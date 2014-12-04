<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Stream\StreamInterface;
use paslandau\ArrayUtility\ArrayUtil;
use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\GuzzleResponseData;
use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions as paslandauE;

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
        $this->responseData = $this->getMock(ResponseInterface::class);
        $this->headers = array(
            "location" => "http://www.example.com/"
        );
        $this->body = "body";
        $this->responseData->expects($this->any())->method('getHeaders')->will($this->returnValue($this->headers));
        $streamMock = $this->getMock(StreamInterface::class);
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

    public function test_constructor_true()
    {
        $r = new GuzzleResponseData($this->responseData);
        $this->assertEquals($this->version, $r->getProtocolVersion());
        $this->assertEquals($this->body, $r->getBody());
        $this->assertEquals($this->statusCode, $r->getStatusCode());
        $this->assertEquals($this->url, $r->getUrl());
        $this->assertEquals($this->reasonPhrase, $r->getReasonPhrase());
        $h = $r->getHeaders();
        $res = ArrayUtil::containsArray($this->headers, $h, true, false, true);
        $msg = json_encode($h);
        $this->assertTrue($res, $msg);
    }

    public function test_constructor_false()
    {
        $r = new GuzzleResponseData($this->responseData);
        $this->assertNotEquals($this->version . " wrong", $r->getProtocolVersion());
        $this->assertNotEquals($this->body . " wrong", $r->getBody());
        $this->assertNotEquals($this->statusCode . " wrong", $r->getStatusCode());
        $this->assertNotEquals($this->url . " wrong", $r->getUrl());
        $this->assertNotEquals($this->reasonPhrase . " wrong", $r->getReasonPhrase());
        $out = null;
        $wrongHeaders = ["wrong" => "headers"];
        $res = ArrayUtil::containsArray($wrongHeaders, $r->getHeaders(), true, false, true);
        $msg = json_encode($r->getHeaders());
        $this->assertFalse($res, $msg);
    }

    public function test_getDomDocument_ShouldThrowExceptionOnMissingDomConverter()
    {
        $this->setExpectedException(RuntimeException::class);
        $r = new GuzzleResponseData($this->responseData, null, null);
        $r->getDomDocument();
    }

    public function test_getDomDocument_ShouldCacheDomDocument()
    {
        $converterMock = $this->getMock(DomConverterInterface::class);
        $doc = new DOMDocument();
        $child = new DOMText('foo');
        $doc->appendChild($child);
        // only call convert once - multiple requests should be answered from cache
        $converterMock->expects($this->once())->method("convert")->will($this->returnValue($doc));
        /** @var DomConverterInterface $converterMock */
        $r = new GuzzleResponseData($this->responseData, null, $converterMock);
        $actual = $r->getDomDocument();
        $actualChild = $actual->childNodes->item(0);
        $this->assertEquals($child->nodeValue, $actualChild->nodeValue, "Wrong document returned on first 'convert' call");

        $actual = $r->getDomDocument();
        $actualChild = $actual->childNodes->item(0);
        $this->assertEquals($child->nodeValue, $actualChild->nodeValue, "Wrong document returned on second 'convert' call (should be cached");
    }

    public function test_getHeaders_ShouldReturnLowercaseHeaders()
    {
        $headers = [
            "X-FOO" => "foo",
            "SeRvEr" => "Apache",
            "time" => "...",
            "Set-Cookie" => [
                "foo",
                "bar"
            ]
        ];

        $resp = $this->getMock(ResponseInterface::class);
        $resp->expects($this->once())->method('getHeaders')->will($this->returnValue($headers));
        /** @var  ResponseInterface $resp */
        $guzzleResp = new GuzzleResponseData($resp,null,null);

        $expected = [
            "x-foo" => "foo",
            "server" => "Apache",
            "time" => "...",
            "set-cookie" => [
                "foo",
                "bar"
            ]
        ];
        $actual = $guzzleResp->getHeaders();
        $this->assertSame($expected,$actual,"Header keys should be lowercased");
    }

    public function test_getException_ShouldClassifyCurlExceptions(){
        /** @var  ResponseInterface $resp */
        $resp = $this->getMock(ResponseInterface::class);

        /** @var RequestInterface $requestMock */
        $requestMock = $this->getMock(RequestInterface::class);

        $tests = [
            "null" => [
                "input" => null,
                "expected" => null,
            ],
            "default" => [
                "input" => new RequestException("default",$requestMock),
                "expected" => paslandauE\RequestException::class,
            ],
            "randomException" => [
                "input" => new RuntimeException("random",0,null),
                "expected" => paslandauE\RequestException::class,
            ],
        ];
        $specialExceptions = [
            "timeout" =>
            [
                "expected" => paslandauE\TimeoutException::class,
                "codes" => [
                     CURLE_PARTIAL_FILE => "CURLE_PARTIAL_FILE ",
                    CURLE_COULDNT_RESOLVE_HOST => "CURLE_COULDNT_RESOLVE_HOST ",
                    CURLE_COULDNT_CONNECT => "CURLE_COULDNT_CONNECT ",
                    CURLE_OPERATION_TIMEOUTED => "CURLE_OPERATION_TIMEDOUT ",
                    CURLE_SEND_ERROR => "CURLE_SEND_ERROR ",
                    CURLE_RECV_ERROR => "CURLE_RECV_ERROR ",
                ]
            ],
            "compression" =>
                [
                    "expected" => paslandauE\CompressionException::class,
                    "codes" => [
                        CURLE_WRITE_ERROR => "CURLE_WRITE_ERROR"
                    ]
                ],
        ];
        foreach($specialExceptions as $type => $data){
            foreach($data["codes"] as $code => $codeName){
                $patterns = ["cURL error %s foo text","[curl] (#%s) foo text"];
                foreach($patterns as $key => $p) {
                    $tests[$type . "_" . $codeName."_pattern_$key"] = [
                        "input" => new RequestException(sprintf($p,$code), $requestMock),
                        "expected" => $data["expected"],
                    ];
                }
            }
        }
        foreach ($tests as $name => $data) {
            /** @var RequestException $input */
            $input = $data["input"];
            $expected = null;
            if($data["expected"] !== null) {
                $expected = new $data["expected"]($input->getMessage(), null, $input);
            }
            $actual = null;
            $guzzleResp = new GuzzleResponseData($resp,$input,null);
            try {
                $actual = $guzzleResp->getException();
            } catch (Exception $e) {
                $actual = get_class($e);
            }

            $msg = [
                $name,
                "Input   : " . json_encode($this->getExceptionInfo($input)),
                "Expected: " . json_encode($this->getExceptionInfo($expected)),
                "Actual  : " . json_encode($this->getExceptionInfo($actual)),
            ];
            $msg = implode("\n", $msg);
            $this->assertEquals($expected, $actual, $msg);
        }
    }

    private function getExceptionInfo(Exception $e = null){
        if($e === null){
            return null;
        }
        return [
            "type" => get_class($e),
            "message" => $e->getMessage(),
            "previous" => $this->getExceptionInfo($e->getPrevious())
        ];
    }
}
