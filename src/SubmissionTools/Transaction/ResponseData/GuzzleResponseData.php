<?php
namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions as paslandauE;
use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\CompressionException;
use paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\TimeoutException;

class GuzzleResponseData implements ResponseDataInterface
{

    /**
     * @var ResponseInterface
     */
    private $guzzleResponse;

    /**
     * @var \Exception|null
     */
    private $requestException;

    /**
     * @var \DomDocument|null
     */
    private $domDocCache;

    /**
     * @var DomConverterInterface|null
     */
    private $domConverter;

    /**
     * @param ResponseInterface|null $guzzleResponse
     * @param \Exception|null $requestException [optional]. Default: null.
     * @param DomConverterInterface|null $domConverter [optional]. Default: null.
     */
    public function __construct(ResponseInterface $guzzleResponse = null, \Exception $requestException = null, DomConverterInterface $domConverter = null)
    {
        $this->setGuzzleResponse($guzzleResponse);
        $this->requestException = $requestException;
        $this->domDocCache = null;
        $this->domConverter = $domConverter;
    }

    /**
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function getGuzzleResponse()
    {
        return $this->guzzleResponse;
    }

    /**
     * @param \GuzzleHttp\Message\ResponseInterface $guzzleResponse
     */
    public function setGuzzleResponse($guzzleResponse = null)
    {
        $this->guzzleResponse = $guzzleResponse;
    }

    /**
     * Caution: The request that created the response must provide a suitable DomConverterInterface!
     * @return \DOMDocument
     */
    public function getDomDocument()
    {
        if($this->guzzleResponse === null){
            return null;
        }
        if ($this->domConverter === null) {
            throw new \RuntimeException("No DomConverter provided in the request");
        }
        if ($this->domDocCache === null) {
            $this->domDocCache = $this->domConverter->convert($this->getBody());
        }
        /** @var \DOMDocument $d */
        $d = $this->domDocCache->cloneNode(true);
        return $d;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        if($this->guzzleResponse === null){
            return "";
        }
        $stream = $this->guzzleResponse->getBody();
        if ($stream === null) {
            return "";
        }
        return $stream->__toString();
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        if($this->guzzleResponse === null){
            return 0;
        }
        return $this->guzzleResponse->getStatusCode();
    }

    /**
     * @return mixed[]
     */
    public function getHeaders()
    {
        $headers = [];
        if($this->guzzleResponse === null){
            return [];
        }
        $guzzleHeaders = $this->guzzleResponse->getHeaders();
        foreach($guzzleHeaders as $h => $v){
            $lc = mb_strtolower($h);
            $headers[$lc] = $v;
        }
        return $headers;
    }

    /**
     * Last URL during the request
     * @return string
     */
    public function getUrl()
    {
        if($this->guzzleResponse === null){
            return "";
        }
        return $this->guzzleResponse->getEffectiveUrl();
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        if($this->guzzleResponse === null){
            return "";
        }
        return $this->guzzleResponse->getReasonPhrase();
    }

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        if($this->guzzleResponse === null){
            return "";
        }
        return $this->guzzleResponse->getProtocolVersion();
    }


    /**
     * @return null|\paslandau\WebAutomator\SubmissionTools\Transaction\Exceptions\RequestException
     */
    public function getException()
    {
        // convert Exception
        if ($this->requestException === null) {
            return null;
        }
        if ($this->requestException instanceof RequestException) {

            // todo: wait for a better solution from guzzle to identify timeouts
            // @see http://stackoverflow.com/questions/25661591/php-how-to-check-for-timeout-exception-in-guzzle-4
            // for now, let's assume that guzzle passes on the curl error codes (of course, this will only work with the curl adapter
            // @see http://curl.haxx.se/libcurl/c/libcurl-errors.html for error codes
            // CURLE_PARTIAL_FILE (18)
            // CURLE_COULDNT_RESOLVE_HOST (6)
            // CURLE_COULDNT_CONNECT (7)
            // CURLE_OPERATION_TIMEDOUT (28) [most common]
            // CURLE_SEND_ERROR (55)
            // CURLE_RECV_ERROR (56)
            if($this->requestException->getCode() === 0 && preg_match("#cURL error (6|7|28|18|55|56)|\\[curl\\] \\(\\#(6|7|28|18|55|56)\\)#i", $this->requestException->getMessage())) {
                return new TimeoutException(
                    $this->requestException->getMessage(), $this->requestException->getCode(), $this->requestException
                );
            }
            // cURL error 23: Error while processing content unencoding: invalid stored block lengths
            // cURL error 23: Error while processing content unencoding: invalid code lengths set
            if($this->requestException->getCode() === 0 && preg_match("#cURL error (23)|\\[curl\\] \\(\\#(23)\\)#i", $this->requestException->getMessage())) {
                return new CompressionException(
                    $this->requestException->getMessage(), $this->requestException->getCode(), $this->requestException
                );
            }
        }
        return new paslandauE\RequestException(
            $this->requestException->getMessage(), $this->requestException->getCode(), $this->requestException
        );
    }
}