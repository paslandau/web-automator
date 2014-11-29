<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\DataFiltering\Identification\IdentificationInterface;

class ErrorStep extends AbstractBaseSubmissionStep implements ErrorStepInterface
{
    public static function init($id){
        return new self($id);
    }

    /**
     * @param IdentificationInterface $identification
     */
    function __construct(IdentificationInterface $identification)
    {
        parent::__construct($identification);
    }
}