<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Event\AbstractTransferEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Post\PostBodyInterface;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Retry\RetrySubscriber;
use paslandau\GuzzleAutoCharsetEncodingSubscriber\GuzzleAutoCharsetEncodingSubscriber;
use paslandau\DomUtility\DomConverterInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\GuzzleSession;
use paslandau\DataFiltering\Traits\LoggerTrait;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\GuzzleResponseData;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfigInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

class GuzzleRequestInfo implements RequestInfoInterface
{
    use LoggerTrait;
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $url;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * @var mixed[]
     */
    private $headers;

    /**
     * @var mixed[]
     */
    private $options;

    /**
     * @var DomConverterInterface
     */
    private $domConverter;

    /**
     * @return EncodingConverterInterface
     */
    private $encodingConverter;

    /**
     * @var ResponseValidatorConfigInterface
     */
    private $responseValidators;

    /**
     * @var GuzzleSession
     */
    private $session;

    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $payload [optional]. Default: null.
     * @param mixed[] $headers [optional]. Default: null.
     * @param mixed[] $options [optional]. Default: null.
     * @param ResponseValidatorConfigInterface $responseValidators [optional]. Default: null.
     * @param DomConverterInterface $domConverter [optional]. Default: null.
     * @param EncodingConverterInterface $encodingConverter
     * @param GuzzleSession $session [optional]. Default: null.
     */
    function __construct($method, $url, $payload = null, $headers = null, $options = null, ResponseValidatorConfigInterface $responseValidators = null, DomConverterInterface $domConverter = null, EncodingConverterInterface $encodingConverter = null, GuzzleSession $session = null)
    {
        $this->method = $method;
        $this->url = $url;
        $this->payload = $payload;
        $this->headers = $headers;
        $this->options = $options;
        $this->domConverter = $domConverter;
        $this->responseValidators = $responseValidators;
        $this->encodingConverter = $encodingConverter;
        $this->session = $session;
    }

    // TODO: This should become obsolete once we got a proper GuzzleAdapter
    public static function fromRequestInfo(RequestInfoInterface $info)
    {
        $guzzleRequest = new self(
            $info->getMethod(),
            $info->getUrl(),
            $info->getPayload(),
            $info->getHeaders(),
            $info->getOptions(),
            $info->getResponseValidators(),
            $info->getDomConverter(),
            $info->getEncodingConverter()
        );
        $session = $info->getSession();
        if ($session !== null && !$session instanceof GuzzleSession) {
            throw new \InvalidArgumentException("Session has to be of type GuzzleSession not " . gettype($session));
        }
        $guzzleRequest->setSession($session);
        return $guzzleRequest;
    }

    public function GetGuzzleRequest(ClientInterface $guzzleClient)
    {
        $options = array();
        if ($this->getOptions() !== null) {
            $options = $this->getOptions();
        }
        if ($this->getPayload() !== null) {
            $m = $this->getMethod();
            $m = mb_strtoupper($m);
            // @todo: have an eye on this.. maybe it's necessaray to introduce a getQuery method to clearly distinct between query and body
            // @see http://stackoverflow.com/questions/299628/is-an-entity-body-allowed-for-an-http-delete-request
            // @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
            if (in_array($m, ["POST", "PUT", "PATCH", "OPTIONS"])) {
                $options['body'] = $this->getPayload();
            } else {
                $options['query'] = $this->getPayload();
            }
        }
        if ($this->getHeaders() !== null) {
            $options['headers'] = $this->getHeaders();
        }
        if ($this->session !== null) {
            // add session
            /** @var GuzzleSession ($this->session) */
            $sessionOptions = $this->session->getGuzzleOptions();
            $options = array_merge_recursive($options, $sessionOptions); //todo: array_merge or array_replace?
        }
        $request = $guzzleClient->createRequest($this->getMethod(), $this->getUrl(), $options);
        $request->getConfig()->set('domConverter', $this->getDomConverter());
        $this->attachRetrySubscriber($request);
        $this->attachFixEncodingSubscriber($request);
        return $request;
    }

    /**
     * @return \mixed[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed[] $options
     */
    public function setOptions(array $options = null)
    {
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload = null)
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed[] $headers
     */
    public function setHeaders(array $headers = null)
    {
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return DomConverterInterface|null
     */
    public function getDomConverter()
    {
        return $this->domConverter;
    }

    /**
     * @param DomConverterInterface $domConverter
     */
    public function setDomConverter($domConverter)
    {
        $this->domConverter = $domConverter;
    }

    /**
     * @return mixed
     */
    public function getEncodingConverter()
    {
        return $this->encodingConverter;
    }

    /**
     * @param EncodingConverterInterface $encodingConverter
     */
    public function setEncodingConverter(EncodingConverterInterface $encodingConverter)
    {
        $this->encodingConverter = $encodingConverter;
    }

    /**
     * @return ResponseValidatorConfigInterface
     */
    public function getResponseValidators()
    {
        return $this->responseValidators;
    }

    /**
     * @param ResponseValidatorConfigInterface $validators
     * @return void
     */
    public function setResponseValidators(ResponseValidatorConfigInterface $validators)
    {
        $this->responseValidators = $validators;
    }

    /**
     * @return GuzzleSession|null
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param GuzzleSession $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    public function mergeWithDefaults(RequestInfoInterface $requestInfo)
    {
        if ($requestInfo === null) {
            return;
        }
        foreach ($this as $prop => $val) {
            if ($val === null) { // override null values in any case
                $this->{$prop} = $requestInfo->{$prop};
            }
            if ($requestInfo->{$prop} !== null && is_array($requestInfo->{$prop})) { // merge arrays
                if (is_array($val)) { // note: if $val was not set [= null] , it would have been overriden before
                    $this->{$prop} = array_replace_recursive($requestInfo->{$prop}, $val);
                }
            }
        }
    }

    private function attachRetrySubscriber(RequestInterface $request)
    {
        if ($this->responseValidators !== null) {
            $emitter = $request->getEmitter();
            $filters = array();
            foreach ($this->responseValidators->getResponseValidators() as $validator) {
                $filters[] = function ($retries, AbstractTransferEvent $event) use ($validator) {
//                    echo "In Validation. Request:".md5(spl_object_hash($event->getRequest()))." Event: ".md5(spl_object_hash($event))."\n";
                    $guzzle_response = null;
                    $exception = null;
                    $request = $event->getRequest();
                    if ($event instanceof CompleteEvent) {
                        $guzzle_response = $event->getResponse();
                    }
                    if ($event instanceof ErrorEvent) {
                        $exception = $event->getException();
                    }
                    $domConverter = $request->getConfig()->get('domConverter');
                    $response = new GuzzleResponseData($guzzle_response, $exception, $domConverter);
                    $isValid = $validator->isValid($response);

                    if (!$isValid) {
                        // OMG this is ugly!
                        $config = $request->getConfig()->toArray();
                        $body = $request->getBody();
                        if ($body !== null) {
                            if ($body instanceof PostBodyInterface) {
                                $body = $body->getFields();
                            } else {
                                $body = $body->__toString();
                            }
                        } else {
                            $query = $request->getQuery();
                            if (!$query !== null && count($query) > 0) {
                                $body = $query;
                            }
                        }
                        $headers = $request->getHeaders();
                        $requestInfo = new SimpleRequestInfo(
                            $request->getMethod(),
                            $request->getUrl(),
                            $body,
                            $headers,
                            $config
                        );
                        $validator->fixRequest($requestInfo);

                        // Note: We can only modify Method, URL, Body, Query and Headers
                        // - no "internal" config settings of the request [see private GuzzleHttp\Message\Request::$transferOptions] like "curl" specific options
                        $m = mb_strtoupper($requestInfo->getMethod());
                        $request->setMethod($m);
                        $request->setUrl($requestInfo->getUrl());
                        if ($requestInfo->getPayload() !== null) {
                            if (in_array($m, ["POST", "PUT", "PATCH", "OPTIONS"])) {
                                $body = $request->getBody();
                                if ($body instanceof PostBodyInterface) {
                                    $body->replaceFields($requestInfo->getPayload());
                                } else {
                                    $body = Stream::factory($requestInfo->getPayload());
                                }
                                $request->setBody($body);
                            } else {
                                $request->setQuery($requestInfo->getPayload());
                            }
                        }
                        $headers = $requestInfo->getHeaders();
                        if ($headers !== null) {
                            $request->setHeaders($headers);
                        }
                        $this->getLogger()->debug("Retrying {$event->getRequest()->getUrl()} for the " . ($retries + 1) . ". time...");
                    }

                    return !$isValid; //retry if invalid
                };
            }
            $subscriber = new RetrySubscriber([
                'filter' => RetrySubscriber::createChainFilter($filters),
                'max' => $this->responseValidators->getMaxRetries()
            ]);
            $emitter->attach($subscriber);
        }
    }

    private function attachFixEncodingSubscriber(RequestInterface $request)
    {
        if ($this->encodingConverter === null) {
            return;
        }
        $sub = new GuzzleAutoCharsetEncodingSubscriber($this->encodingConverter);
        $request->getEmitter()->attach($sub);
    }
}