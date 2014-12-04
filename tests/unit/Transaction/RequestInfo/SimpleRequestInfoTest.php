<?php

use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions as paslandauE;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\SimpleRequestInfo;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfig;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfigInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

class SimpleRequestInfoTest extends PHPUnit_Framework_TestCase
{

    public function test_mergeWithDefaults()
    {

        /** @var SessionInterface $sessionMock */
        $sessionMock = $this->getMock(SessionInterface::class);
        $sessionMock->original = "foo"; // make sure there's a difference to input so that they are not equal

        /** @var ResponseValidatorConfigInterface $validatorsMock */
        $validatorsMock = $this->getMock(ResponseValidatorConfigInterface::class);
        $validatorsMock->original = "foo";

        /** @var DomConverterInterface $converterMock */
        $converterMock = $this->getMock(DomConverterInterface::class);
        $converterMock->original = "foo";

        /** @var EncodingConverterInterface $encodingConverterMock */
        $encodingConverterMock = $this->getMock(EncodingConverterInterface::class);
        $encodingConverterMock->original = "foo";

        /** @var SessionInterface $sessionMockInput */
        $sessionMockInput = $this->getMock(SessionInterface::class);
        /** @var ResponseValidatorConfigInterface $validatorsMockInput */
        $validatorsMockInput = $this->getMock(ResponseValidatorConfigInterface::class);
        /** @var DomConverterInterface $converterMockInput */
        $converterMockInput = $this->getMock(DomConverterInterface::class);
        /** @var EncodingConverterInterface $encodingConverterMockInput */
        $encodingConverterMockInput = $this->getMock(EncodingConverterInterface::class);

        $tests = [
            "null" => [
                "original" => [
                    "method" => "GET",
                    "url" => "http://www.example.com",
                    "payload" => ["foo" => "bar"],
                    "headers" => ["x-foo" => "bar"],
                    "options" => ["baz" => ["test" => 1]],
                    "validators" => $validatorsMock,
                    "domConverter" => $converterMock,
                    "encodingConverter" => $encodingConverterMock,
                    "session" => $sessionMock
                ],
                "input" => null,
                "expected" => new SimpleRequestInfo(
                    "GET",
                    "http://www.example.com",
                    ["foo" => "bar"],
                    ["x-foo" => "bar"],
                    ["baz" => ["test" => 1]],
                    $validatorsMock,
                    $converterMock,
                    $encodingConverterMock,
                    $sessionMock
                ),
            ],
            "default" => [
                "original" => [
                    "method" => "GET",
                    "url" => "http://www.example.com",
                    "payload" => ["foo" => "bar"],
                    "headers" => ["x-foo" => "bar"],
                ],
                "input" => new SimpleRequestInfo(
                    "", "", ["foo2" => "bar2"], ["x-foo2" => "bar2"]
                ),
                "expected" => new SimpleRequestInfo(
                    "GET", "http://www.example.com", ["foo" => "bar", "foo2" => "bar2"], ["x-foo" => "bar", "x-foo2" => "bar2"]
                ),
            ],
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
                    "session" => $sessionMock
                ],
                "input" => new SimpleRequestInfo(
                    "POST", "http://www.google.de", ["foo2" => "bar2"], ["x-foo2" => "bar2"], ["baz" => "foo"],
                    $validatorsMockInput,
                    $converterMockInput,
                    $encodingConverterMockInput,
                    $sessionMockInput
                ),
                "expected" => new SimpleRequestInfo(
                    "GET", "http://www.example.com", ["foo" => "bar", "foo2" => "bar2"], ["x-foo" => "bar", "x-foo2" => "bar2"], ["baz" => ["test" => 1]],
                    $validatorsMock,
                    $converterMock,
                    $encodingConverterMock,
                    $sessionMock
                ),
            ],
            "all-override" => [
                "original" => [
                ],
                "input" => new SimpleRequestInfo(
                    "POST", "http://www.google.de", ["foo2" => "bar2"], ["x-foo2" => "bar2"], ["baz" => "foo"],
                    $validatorsMockInput,
                    $converterMockInput,
                    $encodingConverterMockInput,
                    $sessionMockInput
                ),
                "expected" => new SimpleRequestInfo(
                    "POST", "http://www.google.de", ["foo2" => "bar2"], ["x-foo2" => "bar2"], ["baz" => "foo"],
                    $validatorsMockInput,
                    $converterMockInput,
                    $encodingConverterMockInput,
                    $sessionMockInput
                ),
            ],
        ];

        foreach ($tests as $name => $data) {
            $original = $data["original"];
            $input = $data["input"];
            $expected = $data["expected"];

            $default = new SimpleRequestInfo(
                (array_key_exists("method",$original)  ? $original["method"] : null),
                (array_key_exists("url",$original)  ? $original["url"] : null),
                (array_key_exists("payload",$original)  ? $original["payload"] : null),
                (array_key_exists("headers",$original)  ? $original["headers"] : null),
                (array_key_exists("options",$original)  ? $original["options"] : null),
                (array_key_exists("validators",$original)  ? $original["validators"] : null),
                (array_key_exists("domConverter",$original)  ? $original["domConverter"] : null),
                (array_key_exists("encodingConverter",$original)  ? $original["encodingConverter"] : null),
                (array_key_exists("session",$original)  ? $original["session"] : null));
            $request = new SimpleRequestInfo(
                (array_key_exists("method",$original)  ? $original["method"] : null),
                (array_key_exists("url",$original)  ? $original["url"] : null),
                (array_key_exists("payload",$original)  ? $original["payload"] : null),
                (array_key_exists("headers",$original)  ? $original["headers"] : null),
                (array_key_exists("options",$original)  ? $original["options"] : null),
                (array_key_exists("validators",$original)  ? $original["validators"] : null),
                (array_key_exists("domConverter",$original)  ? $original["domConverter"] : null),
                (array_key_exists("encodingConverter",$original)  ? $original["encodingConverter"] : null),
                (array_key_exists("session",$original)  ? $original["session"] : null));

            $actual = null;
            try {
                $request->mergeWithDefaults($input);
                $actual = $request;
            } catch (Exception $e) {
                $actual = get_class($e);
            }

            $msg = [
                $name,
                "Default : " . json_encode($this->getRequestInfoString($default)),
                "Input   : " . json_encode($this->getRequestInfoString($input)),
                "Expected: " . json_encode($this->getRequestInfoString($expected)),
                "Actual  : " . json_encode($this->getRequestInfoString($actual)),
            ];
            $msg = implode("\n", $msg);
            $this->assertEquals($expected, $actual, $msg);
        }
    }

    private function getRequestInfoString(RequestInfoInterface $info = null)
    {
        if ($info === null) {
            return null;
        }

        $reflectionClass = new \ReflectionClass ($info);
        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        $res = [];
        foreach ($methods as $method) {
            $first = mb_substr($method->name, 0, 3);
            $last = mb_substr($method->name, 3);
            $m = ["get", "has"];
            if (in_array($first, $m)) {
                $res[$last] = $method->invoke($info);
            }
        }
        return $res;
    }
}
