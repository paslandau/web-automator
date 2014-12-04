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
use paslandau\DataFiltering\Traits\LoggerTrait;
use paslandau\DomUtility\DomConverterInterface;
use paslandau\GuzzleAutoCharsetEncodingSubscriber\GuzzleAutoCharsetEncodingSubscriber;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\GuzzleSession;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\GuzzleResponseData;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\ResponseValidatorConfigInterface;
use paslandau\WebUtility\EncodingConversion\EncodingConverterInterface;

class GuzzleRequestInfo extends AbstractBaseRequestInfo implements RequestInfoInterface
{
    use LoggerTrait;

    /**
     * @var null|GuzzleSession
     */
    private $session;

    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $payload [optional]. Default: null.
     * @param mixed[] $headers [optional]. Default: null.
     * @param mixed[] $options [optional]. Default: null.
     * @param null|ResponseValidatorConfigInterface $responseValidators [optional]. Default: null.
     * @param null|DomConverterInterface $domConverter [optional]. Default: null.
     * @param null|EncodingConverterInterface $encodingConverter [optional]. Default: null.
     * @param null|GuzzleSession $session [optional]. Default: null.
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
            throw new \InvalidArgumentException("Session has to be of type GuzzleSession not " . get_class($session));
        }
        $guzzleRequest->setSession($session);
        return $guzzleRequest;
    }

    public function getGuzzleRequest(ClientInterface $guzzleClient)
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
     * @return GuzzleSession|null
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param null|GuzzleSession $session
     */
    public function setSession($session = null)
    {
        $this->session = $session;
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