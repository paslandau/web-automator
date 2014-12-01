<?php
namespace paslandau\WebAutomator\SubmissionTools\Submission;


use paslandau\DataFiltering\Traits\HasEventDispatcher;
use paslandau\DataFiltering\Traits\HasEventDispatcherInterface;
use paslandau\DataFiltering\Traits\LoggerTrait;
use paslandau\ExceptionUtility\ExceptionUtil;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionFactoryInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionWorker;
use paslandau\WebAutomator\SubmissionTools\Submission\Session\SessionWorkerInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepQueueInterface;

abstract class AbstractSubmissionManager implements HasEventDispatcherInterface
{
    use LoggerTrait;
    use HasEventDispatcher;

    /**
     * @var SubmitterInterface
     */
    protected $submitter;

    /**
     * @var SessionInterface[]
     */
    protected $sessions;

    /**
     * @var SessionFactoryInterface
     */
    protected $sessionFactory;

    function __construct(SubmitterInterface $submitter, SessionFactoryInterface $sessionFactory)
    {
        $this->submitter = $submitter;
        $this->sessionFactory = $sessionFactory;
        $this->sessions = array();
    }

    public function createSessions($num)
    {
        for ($i = 0; $i < $num; $i++) {
            $session = $this->sessionFactory->createSession();
            $this->setSession($i, $session);
        }
    }

    public function setSession($id, SessionInterface $session)
    {
        $this->sessions[$id] = $session;
    }

    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Implement this method to perform a setup action before callMethod is executed. Example: Login sessions.
     */
    abstract protected function setUp();

    protected function callMethod($method, array $data, callable $mappingFunction = null, $eventName = null, callable $validateOnError = null, callable $validateOnSuccess = null, $maxRetries = null)
    {
        $result = [];
        $retries = [];
        $maxRetries = $maxRetries === null ? 0 : $maxRetries;
        while (count($data) > 0) {
            $this->setUp();
            $workers = $this->assignData($data);
            $data = array_slice($data, count($workers), null, true); // remove current data from data list
            $queues = $this->getQueues($workers, $method);
            $queues = $this->submitter->run($queues);
            $this->assignResults($queues, $workers, $eventName, $validateOnError, $validateOnSuccess);
            $results = $this->reQueueFailed($workers, $data, $retries, $maxRetries); // add failed requests again
            if ($mappingFunction !== null) {
                $results = $mappingFunction($results);
            }
            $result += $results;
        }
        return $result;
    }

    protected function assignData(array $tmpData)
    {
        $workers = [];
        foreach ($this->sessions as $id => $session) {
            if (count($tmpData) <= 0) {
                break;
            }
            $data = reset($tmpData);
            $dataId = key($tmpData);
            $workers[$id] = new SessionWorker($id, $session, $dataId, $data);
            unset($tmpData[$dataId]);
        }
        return $workers;
    }

    /**
     * @param SessionWorkerInterface[] $workers
     * @param callable $method
     * @return array
     */
    protected function getQueues(array $workers, $method)
    {
        $queues = [];
        foreach ($workers as $id => $worker) {
            $session = $worker->getSession();
            $params = $worker->getData();
            $params["session"] = $session;
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
            /** @var SessionWorkerInterface $worker */
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
     * @param SessionWorkerInterface $worker
     * @param SubmissionStepQueueInterface $queue
     * @param callable $validateOnError
     * @param callable $validateOnSuccess
     * @return bool
     */
    protected function validateQueue(SessionWorkerInterface $worker, SubmissionStepQueueInterface $queue, callable $validateOnError = null, callable $validateOnSuccess = null)
    {
        if (($e = $queue->getException()) !== null) {
            if ($validateOnError !== null) {
                $validateOnError($worker, $queue, $e);
            }
        } elseif($validateOnSuccess !== null){
            $validateOnSuccess($worker, $queue, $queue->getResult());
        }
        return true;
    }

    /**
     * @param SessionWorkerInterface[] $workers
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
            if ($worker->getError() !== null) {
                if (!array_key_exists($dataId, $retries)) {
                    $retries[$dataId] = 0;
                }
                $tryCount = $retries[$dataId];
                if ($tryCount < $maxRetries) {
                    $this->getLogger()->debug("Retrying '$dataId' for the {$retries[$dataId]} time.");
                    $retries[$dataId]++;
                    $data[$dataId] = $worker->getData();
                }
            }
            $results[$dataId] = $worker->getResult();
        }
        return $results;
    }
}