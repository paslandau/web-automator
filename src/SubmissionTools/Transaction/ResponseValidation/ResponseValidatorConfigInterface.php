<?php
namespace paslandau\WebAutomator\SubmissionTools\Transaction\ResponseValidation;

interface ResponseValidatorConfigInterface
{
    /**
     * @return int
     */
    public function getMaxRetries();

    /**
     * @return ResponseValidatorInterface[]
     */
    public function getResponseValidators();

}