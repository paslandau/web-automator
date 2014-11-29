<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission;


use GuzzleHttp\Client;
use GuzzleHttp\Event\AbstractTransferEvent;
use GuzzleHttp\Event\EndEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Pool;
use paslandau\WebAutomator\SubmissionTools\Submission\Debug\Debug;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepQueue;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepQueueInterface;
use paslandau\DataFiltering\Traits\LoggerTrait;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\GuzzleRequestInfo;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\GuzzleResponseData;

class GuzzleSubmitter implements SubmitterInterface
{
    use LoggerTrait;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var  integer
     */
    private $parallel;

    /**
     * @var Debug
     *
     */
    private $debug;

    function __construct(Client $client, $parallel, Debug $debug = null)
    {
        $this->client = $client;
        $this->debug = $debug;
        if ($parallel === null) {
            $parallel = 25;
        }
        $this->parallel = $parallel;
    }

    /**
     * @param SubmissionStepQueueInterface[] $submissionQueueList
     * @return SubmissionStepQueueInterface[]
     */
    public function run(array $submissionQueueList)
    {
        if ($this->debug !== null && $this->debug->getLevel() !== Debug::LEVEL_NONE) {
            $this->debug->activateDebug($submissionQueueList);
        }

        do {
            $requests = [];
            foreach ($submissionQueueList as $key => $m) {
                $requestInfo = $m->getNextRequest();
                if ($requestInfo !== null) {
                    $guzzleInfo = GuzzleRequestInfo::fromRequestInfo($requestInfo);
                    $request = $guzzleInfo->GetGuzzleRequest($this->client);
//                    echo "III Before send. Request:".md5(spl_object_hash($request))."\n";
                    $requests[$key] = $request;
                    $request->getConfig()->set('id', $key);
                }
            }
            if (count($requests) == 0) {
                break;
            }

            // @see https://github.com/guzzle/guzzle/issues/820
            $seen = array();
            $hasSeen = function ($key) use (&$seen) {
                if (array_key_exists($key, $seen)) {
                    $this->getLogger()->debug("Disregarding already known event...");
                    return true;
                }
                $seen[$key] = $key;
                return false;
            };
            $isRedirect = function (RequestInterface $req, ResponseInterface $resp = null) {
                $c = $req->getConfig()->toArray();
                if (array_key_exists("redirect", $c) && $resp !== null && substr($resp->getStatusCode(), 0, 1) === 3) {
                    $this->getLogger()->debug("Ignoring redirect event...");
                    return true;
                }
                return false;
            };
            $evaluateQueueFn = function (AbstractTransferEvent $event) use (&$requests, $submissionQueueList, $hasSeen, $isRedirect) {
//                $this->getLogger()->debug(get_class($event));
                $request = $event->getRequest();
                $response = $event->getResponse();
                $error = null;
                if ($event instanceof ErrorEvent || $event instanceof EndEvent) {
                    $error = $event->getException();
                }
                $key = $request->getConfig()->get('id');
                if ($hasSeen($key) || $isRedirect($request, $response)) {
                    return;
                }
                unset($requests[$key]); // unset request so we now we processed it
                if ($this->debug !== null && $this->debug->getLevel() !== Debug::LEVEL_NONE) {
                    $this->debug->addDebugEvent($event, $key);
                }
                /** @var SubmissionStepQueue $m */
                $m = $submissionQueueList[$key];
                $domConverter = $request->getConfig()->get('domConverter');
                $responseData = new GuzzleResponseData($response, $error, $domConverter);
                try {
                    if ($m->evaluate($responseData)) {
                        $this->getLogger()->debug("Sucessfully evaluated {$m->getCurrentStepIndex()} on " . $response->getEffectiveUrl() . " after requesting " . $request->getUrl() . " of " . $m->getCurrentStepIndex());
                    } else {
                        $targetUrl = "";
                        if ($response !== null) {
                            $targetUrl = "on " . $response->getEffectiveUrl();
                        }
                        $this->getLogger()->debug("Failed to evaluate a step $targetUrl after requesting " . $request->getUrl() . " (previous step " . $m->getCurrentStepIndex());
                    }
                } catch (\Exception $e) {
                    $message = "Error while evaluation a step after requesting " . $request->getUrl() . " (previous step " . $m->getCurrentStepIndex() . ") with message '{$e->getMessage()}'";
                    $ee = new \Exception($message, $e->getCode(), $e);
                    $m->fail($ee);
                    $this->getLogger()->debug($message);
                }
            };
            $options = [
                'pool_size' => $this->parallel,
                'complete' => ["fn" => $evaluateQueueFn, "priority" => RequestEvents::LATE],
                'error' => ["fn" => $evaluateQueueFn, "priority" => RequestEvents::LATE],
                'end' => ["fn" => $evaluateQueueFn, "priority" => RequestEvents::LATE],
            ];
            $pool = new Pool($this->client, $requests, $options);
            $pool->wait();
            foreach ($requests as $key => $request) { // preventing infinite loops if not all requests have been processed
                /** @var Request $request */
                $m = $submissionQueueList[$key];
                $msg = "The request to " . $request->getUrl() . " has not been handled, probably to some internal error. Preventing further requests of this queue (ID: '{$key}') to stop infinite loops";
                $e = new \Exception($msg);
                $m->fail($e);
                $this->getLogger()->addError($msg);
            }
        } while (true); // breaks if count($requests) == 0

        if ($this->debug !== null && $this->debug->getLevel() !== Debug::LEVEL_NONE) {
            $this->debug->saveDebug($submissionQueueList);
        }
        return $submissionQueueList;
    }

    /**
     * @return int
     */
    public function getParallel()
    {
        return $this->parallel;

    }

    /**
     * @param int $parallel
     */
    public function setParallel($parallel)
    {
        $this->parallel = $parallel;
    }
}