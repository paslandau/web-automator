<?php

use GuzzleHttp\Cookie\CookieJar;
use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\GuzzleSession;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions as paslandauE;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\GuzzleRequestInfo;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\SimpleRequestInfo;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfigInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

class GuzzleRequestInfoTest extends PHPUnit_Framework_TestCase
{

    public function test_fromRequestInfo()
    {

        /** @var SessionInterface $sessionMock */
        $sessionMock = $this->getMock(SessionInterface::class);

        //todo: obviously code smell
        $guzzleSession = new GuzzleSession(new CookieJar());

        /** @var ResponseValidatorConfigInterface $validatorsMock */
        $validatorsMock = $this->getMock(ResponseValidatorConfigInterface::class);

        /** @var DomConverterInterface $converterMock */
        $converterMock = $this->getMock(DomConverterInterface::class);

        /** @var EncodingConverterInterface $encodingConverterMock */
        $encodingConverterMock = $this->getMock(EncodingConverterInterface::class);

        $tests = [
            "all-no-override" => [
                "original" => [
                    "method" => "GET",
                    "url" => "http://www.example.com",
                    "payload" => ["foo" => "bar"],
                    "headers" => ["x-foo" => "bar"],
                    "options" => ["baz" => ["test" => 1]],
                    "validators" => $validatorsMock,
                    "domConverter" => $converterMock,
                    "encodingConverter" => $encodingConverterMock,
                    "session" => $guzzleSession
                ],
                "expected" => new GuzzleRequestInfo(
                    "GET", "http://www.example.com",
                    ["foo" => "bar"],
                    ["x-foo" => "bar"],
                    ["baz" => ["test" => 1]],
                    $validatorsMock,
                    $converterMock,
                    $encodingConverterMock,
                    $guzzleSession
                ),
            ],
            "no-guzzle-session" => [
                "original" => [
                    "method" => "GET",
                    "url" => "http://www.example.com",
                    "session" => $sessionMock
                ],
                "expected" => InvalidArgumentException::class
            ],
        ];

        foreach ($tests as $name => $data) {
            $original = $data["original"];
            $expected = $data["expected"];

            $request = new SimpleRequestInfo(
                (array_key_exists("method", $original) ? $original["method"] : null),
                (array_key_exists("url", $original) ? $original["url"] : null),
                (array_key_exists("payload", $original) ? $original["payload"] : null),
                (array_key_exists("headers", $original) ? $original["headers"] : null),
                (array_key_exists("options", $original) ? $original["options"] : null),
                (array_key_exists("validators", $original) ? $original["validators"] : null),
                (array_key_exists("domConverter", $original) ? $original["domConverter"] : null),
                (array_key_exists("encodingConverter", $original) ? $original["encodingConverter"] : null),
                (array_key_exists("session", $original) ? $original["session"] : null));

            $actual = null;
            try {
                $actual = GuzzleRequestInfo::fromRequestInfo($request);
            } catch (Exception $e) {
                $actual = get_class($e);
            }

            $msg = [
                $name,
                "Original: " . json_encode($this->getRequestInfoString($request)),
                "Expected: " . json_encode($this->getRequestInfoString($expected)),
                "Actual  : " . json_encode($this->getRequestInfoString($actual)),
            ];
            $msg = implode("\n", $msg);
            $this->assertEquals($expected, $actual, $msg);
        }
    }

    private function getRequestInfoString($info = null)
    {
        if (!$info instanceof RequestInfoInterface) {
            return null;
        }

        $reflectionClass = new \ReflectionClass ($info);
        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        $res = [];
        foreach ($methods as $method) {
            $first = mb_substr($method->name, 0, 3);
            $last = mb_substr($method->name, 3);
            $m = ["get", "has"];
            if (in_array($first, $m) && $method->getNumberOfParameters() === 0) {
                $res[$last] = $method->invoke($info);
            }
        }
        return $res;
    }

//    public function test_getGuzzleRequest(){
//        /**
//         * check if domCoverter is set
//         * header, query, options etc. in the right place in the resulting request?
//         * test different http verbs
//         * subscribers attached?
//         */
//
//    }
}
