<?php

namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation;


class ResponseValidatorConfig implements ResponseValidatorConfigInterface
{
    private $maxRetries;
    private $responseValidators;

    function __construct($responseValidators = null, $maxRetries = null)
    {
        if($maxRetries === null){
            $maxRetries = 3;
        }
        $this->maxRetries = $maxRetries;

        if($responseValidators === null){
            $responseValidators = [];
        }
        $this->responseValidators = $responseValidators;
    }

    /**
     * @return int|null
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * @param int|null $maxRetries
     */
    public function setMaxRetries($maxRetries)
    {
        $this->maxRetries = $maxRetries;
    }

    /**
     * @return ResponseValidatorInterface[]
     */
    public function getResponseValidators()
    {
        return $this->responseValidators;
    }

    /**
     * @param ResponseValidatorInterface[] $responseValidators
     */
    public function setResponseValidators($responseValidators)
    {
        $this->responseValidators = $responseValidators;
    }
}