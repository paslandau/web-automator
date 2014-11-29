<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Recovery;


use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\SimpleRequestInfo;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoBuilderInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class GenericRecoveryStrategy implements RecoveryStrategyInterface
{

    /**
     * @var mixed
     */
    private $context;

    /**
     * @var callable
     */
    private $function;

    /**
     * @param $context
     * @param callable $function
     * @param null|string $stepIndex
     */
    function __construct($context, callable $function, $stepIndex = null)
    {
        $this->context = $context;
        $this->function = $function;
        $this->stepIndex = $stepIndex;
    }

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @return SimpleRequestInfo
     */
    public function getNextRequest(ResponseDataInterface $responseData)
    {
        $func = $this->function;
        /** @var RequestInfoBuilderInterface $rib */
        $rib = $func($responseData, $this->context);
        if ($rib === null) {
            return null;
        }
        $ri = $rib->buildRequestInfo($responseData);
        return $ri;
    }

    /**
     * @return null|string
     */
    public function getStepIndex()
    {
        return $this->stepIndex;
    }
}