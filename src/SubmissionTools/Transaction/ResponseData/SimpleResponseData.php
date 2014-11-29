<?php
namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData;

use paslandau\ArrayUtility\ArrayUtil;
use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\RequestException;

class SimpleResponseData implements ResponseDataInterface
{

    /**
     * @var string
     */
    private $body;
    /**
     * @var string
     */
    private $statusCode;
    /**
     * @var string
     */
    private $reasonPhrase;
    /**
     * @var string
     */
    private $protocolVersion;
    /**
     * @var string[]
     */
    private $headers;
    /**
     * @var string
     */
    private $url;

    /**
     * @var RequestException
     */
    private $exception;

    /**
     * @var DomConverterInterface
     */
    private $domConverter;


    public function __construct($statusCode, $url, $headers, $body, $protocolVersion, $reasonPhrase, $exception = null, DomConverterInterface $domConverter = null)
    {
        $this->body = $body;
        $this->headers = $headers;
        $this->protocolVersion = $protocolVersion;
        $this->reasonPhrase = $reasonPhrase;
        $this->statusCode = $statusCode;
        $this->url = $url;
        $this->exception = $exception;
        $this->domConverter = $domConverter;
    }

    /**
     * @param GuzzleResponseData $data
     * @return SimpleResponseData
     */
    public static function __constructFromGuzzle(GuzzleResponseData $data)
    {
        $class = get_called_class();

        $body = $data->GetBody();
        $statusCode = $data->GetStatusCode();
        $reasonPhrase = $data->GetReasonPhrase();
        $protocolVersion = $data->GetProtocolVersion();
        $headers = $data->GetHeaders();
        $url = $data->GetUrl();
        $exception = $data->getException();
        return new $class($body, $headers, $protocolVersion, $reasonPhrase, $statusCode, $url, $exception);
    }

    /**
     * @param string $json
     * @return SimpleResponseData
     */
    public static function __constructFromJson($json)
    {
        $json = json_decode($json);
        $body = $json->body;
        $statusCode = $json->statusCode;
        $reasonPhrase = $json->reasonPhrase;
        $protocolVersion = $json->protocolVersion;
        $headers = $json->headers;
        $url = $json->url;
        $exception = null;
        if (property_exists($json, "exception") && $json->exception !== null) {
            $exception = new RequestException($json->exception->message, $json->exception->code);
        }
        return new self($statusCode, $url, $headers, $body, $protocolVersion, $reasonPhrase, $exception);
    }

    /**
     * Transforms the body of the response in a DomDocument.
     * @return \DOMDocument
     */
    public function GetDomDocument()
    {
        if ($this->domConverter !== null) {
            return $this->domConverter->Convert($this->getBody());
        } else {
            throw new \RuntimeException("DomConverter must not be null!");
        }
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return \string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function Equals($obj1)
    {
        if (!$obj1 instanceof self) {
            return false;
        }
        foreach ($this as $key => $prop) {
            if (!is_array($prop)) {
                if ($prop !== $obj1->{$key}) {
                    return false;
                }
            } else {
                $prop1 = (array)$obj1->{$key};
                if (ArrayUtil::equals($prop1, $prop, true, false, true, true)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return null|\paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\RequestException
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\RequestException $exception
     */
    public function setException($exception = null)
    {
        $this->exception = $exception;
    }
}