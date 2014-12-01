<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission;

use Exception;
use paslandau\DataFiltering\Traits\HasEventDispatcher;
use paslandau\DataFiltering\Traits\HasEventDispatcherInterface;
use paslandau\DataFiltering\Traits\LoggerTrait;
use paslandau\ExceptionUtility\ExceptionUtil;
use paslandau\WebAutomator\SubmissionTools\Builders\Build;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepQueueInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\CompressionErrorValidator;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\IncompleteHtmlResponseValidator;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation\TimeoutValidator;
use paslandau\WebAutomator\User\Account\AccountInterface;
use paslandau\WebAutomator\User\Account\AccountSubmissionData;
use paslandau\WebAutomator\User\Account\AccountWorker;
use paslandau\WebAutomator\User\Account\AccountWorkerInterface;

abstract class AbstractAccountSubmissionManager implements HasEventDispatcherInterface
{
    use LoggerTrait;
    use HasEventDispatcher;

    /**
     * @var SubmitterInterface
     */
    protected $submitter;

    /**
     * @var array
     */
    protected $objCount;

    /**
     * @var int
     */
    protected $maxRetries;

    function __construct(SubmitterInterface $submitter, $maxRetries = null)
    {
        $this->submitter = $submitter;
        if ($maxRetries === null) {
            $maxRetries = 1;
        }
        $this->maxRetries = $maxRetries;
        $this->objCount = [];
    }

    public function getEvents()
    {
        return (new \ReflectionClass(get_called_class()))->getConstants();
    }

    /**
     * @param $method
     * @param AccountSubmissionData[] $data
     * @param callable $setUpMethod
     * @param callable $mappingFunction
     * @param null $eventName
     * @param callable $validateOnError
     * @param callable $validateOnSuccess
     * @param null $maxRetries
     * @return SubmissionStepResultInterface[][]
     */
    protected function callMethod($method, array $data, callable $setUpMethod = null, callable $mappingFunction = null, $eventName = null, callable $validateOnError = null, callable $validateOnSuccess = null, $maxRetries = null)
    {
        $result = [];
        $retries = [];
        $maxRetries = $maxRetries === null ? $this->maxRetries : $maxRetries;
        while (count($data) > 0) {
            if ($setUpMethod !== null) {
                $result += $setUpMethod($data, $maxRetries, $eventName);
            }
//            $this->setUp($data);
//            $result += $this->removeInvalidData($data); // remove accounts with failed login
            $workers = $this->assignData($data);
            $data = array_slice($data, count($workers), null, true); // remove current data from data list
            $queues = $this->getQueues($workers, $method);
            $queues = $this->submitter->run($queues);
            $this->assignResults($queues, $workers, $eventName, $validateOnError, $validateOnSuccess);
            $results = $this->reQueueFailed($workers, $data, $retries, $maxRetries); // add failed requests again
            if ($mappingFunction !== null) {
                $results = $mappingFunction($results);
            }
            $result = array_replace($result, $results);
        }
        return $result;
    }

    /**
     * @param AccountSubmissionData[] $accData
     * @return AccountWorkerInterface[]
     */
    protected function assignData(array $accData)
    {
        $workers = [];
        foreach ($accData as $id => $ad) {
            /** @var AccountInterface $acc */
            $workers[$id] = new AccountWorker($id, $ad);
        }
        return $workers;
    }

    /**
     * @param AccountWorkerInterface[] $workers
     * @param callable $method
     * @return SubmissionStepQueueInterface[]
     */
    protected function getQueues(array $workers, $method)
    {
        $queues = [];
        foreach ($workers as $id => $worker) {
            $acc = $worker->getAccountData()->getAccount();
            $params = array_merge(["account" => $acc], $worker->getAccountData()->getData());
            $queue = call_user_func_array($method, $params);
            $queues[$id] = $queue;
        }
        return $queues;
    }

    protected function assignResults($queues, $workers, $eventName = null, callable $validateOnError = null, callable $validateOnSuccess = null)
    {
        $results = [];
        /**
         * @var SubmissionStepQueueInterface $queue
         */
        foreach ($queues as $id => $queue) {
            /** @var AccountWorkerInterface $worker */
            $worker = $workers[$id];
            $dataId = $worker->getDataId();
            $e = null;
            $result = null;
            try {
                $this->validateQueue($worker, $queue, $validateOnError, $validateOnSuccess);
                $result = $queue->getResult();
                $results[$dataId] = $result;
            } catch (\Exception $e) {
                $this->getLogger()->error("Getting results for '$dataId' failed. Error(s):\n" . ExceptionUtil::getAllErrorMessagesAsString($e));
                $results[$dataId] = $e;
            }
            $worker->setResult($result);
            $worker->setError($e);
            if ($eventName !== null) {
                $this->getDispatcher()->dispatch($eventName, new SubmissionResultEvent($worker, $result, $e));
            }
        }
    }

    /**
     * Throws an Exception if an error occurs. Wrap this call in try/catch!
     * @param AccountWorkerInterface $worker
     * @param SubmissionStepQueueInterface $queue
     * @param callable $validateOnError
     * @param callable $validateOnSuccess
     * @return bool
     */
    protected function validateQueue(AccountWorkerInterface $worker, SubmissionStepQueueInterface $queue, callable $validateOnError = null, callable $validateOnSuccess = null)
    {
        if (($e = $queue->getException()) !== null) {
            if ($validateOnError !== null) {
                $validateOnError($worker, $queue, $e);
            }
        } elseif ($validateOnSuccess !== null) {
            $validateOnSuccess($worker, $queue, $queue->getResult());
        }
        return true;
    }

    /**
     * @param AccountWorkerInterface[] $workers
     * @param mixed[] &$data
     * @param int[] &$retries
     * @param $maxRetries
     * @return SubmissionStepResultInterface[][];
     */
    protected function reQueueFailed(array $workers, &$data, array &$retries, $maxRetries)
    {
        $results = [];
        foreach ($workers as $worker) {
            $dataId = $worker->getDataId();
            $error = $worker->getError();
            if ($error !== null) {
                if (!array_key_exists($dataId, $retries)) {
                    $retries[$dataId] = 0;
                }
                $tryCount = $retries[$dataId];
                if ($tryCount < $maxRetries && $this->canBeRetried($worker, $error)) {
                    $this->getLogger()->debug("Retrying '$dataId' for the {$retries[$dataId]} time.");
                    $retries[$dataId]++;
                    $data[$dataId] = $worker->getAccountData();
                }
                $results[$dataId] = $worker->getError();
            } else {
                $results[$dataId] = $worker->getResult();
            }
        }
        return $results;
    }

    /**
     * Override in subclasses! Will be called before a reque on error happens
     * @param AccountWorkerInterface $worker
     * @param Exception $error
     * @return bool
     */
    protected function canBeRetried(AccountWorkerInterface $worker, Exception $error)
    {
        return true;
    }

    protected function getUniqueId($objType)
    {
        if (!array_key_exists($objType, $this->objCount)) {
            $this->objCount[$objType] = 0;
        }
        $this->objCount[$objType]++;
        $prefix = substr(base_convert($this->objCount[$objType], 10, 36), 0, 3) . "_";
        $id = $prefix . substr(md5(uniqid()), 0, 5);
        return $id;
    }

    /**
     * @param $maxRetries
     * @return int
     */
    protected function calculateMaxRetries($maxRetries)
    {
        $maxRetries = $maxRetries !== null ? $maxRetries : $this->maxRetries;
        if ($maxRetries < 0) {
            $maxRetries = 0;
        }
        return $maxRetries;
    }

    protected function getDefaultOptions(AccountInterface $account)
    {

        $options = [];
        $proxy = $account->getProxy();
        if ($proxy !== null) {
            $this->getLogger()->info(get_called_class() . " " . $account->getCredentials()->getUsername() . " " . $proxy);
            $options["proxy"] = $proxy;
        }
        $headers = [];
        $ua = $account->getUserAgent();
        if ($ua !== null) {
            $headers["User-Agent"] = $ua;
        }
        $default = Build::defaultRequest()->convertResponseTo("utf-8")->useHtmlDomConverter(true)->validateResponseBy(
            [new TimeoutValidator(), new IncompleteHtmlResponseValidator(), new CompressionErrorValidator()]
        )->withSession($account->getSession())->headers($headers)->options($options)->build();
        return $default;
    }
} 