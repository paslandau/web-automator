<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Steps;


use paslandau\DataFiltering\Util\StringUtil;
use paslandau\DataFiltering\Traits\HasEventDispatcher;
use paslandau\DataFiltering\Identification\IdentificationInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Events\SubmissionEvent;

abstract class AbstractBaseSubmissionStep implements SubmissionStepInterface{

    use HasEventDispatcher;
    const ON_AFTER_IDENTIFICATION = "on_after_identification";

    /**
     * @var IdentificationInterface
     */
    protected $identification;

    /**
     * @var callable[]
     */
    private $onAfterIdentification;

    /**
     * @param IdentificationInterface $identification
     */
    function __construct(IdentificationInterface $identification)
    {
        $this->identification = $identification;
        $this->onAfterIdentification = array();
    }

    public function setIdentification($identification)
    {
        $this->identification = $identification;
    }

    /**
     * @return \paslandau\DataFiltering\Identification\IdentificationInterface
     */
    public function getIdentification()
    {
        return $this->identification;
    }

    public function __toString()
    {
        $ss = StringUtil::GetObjectString($this);
        return $ss;
    }

    /**
     * @param \paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface $responseData
     * @throws \RuntimeException
     * @return boolean
     */
    public function isIdentifiedBy(ResponseDataInterface $responseData)
    {
        $res = $this->identification->isIdentifiedBy($responseData);
        if($res === true){
            $this->emitOnAfterIdentification($responseData);
        }
        return $res;
    }

    /**
     * @return string
     */
    public function getId(){
        return $this->identification->getIdentifier();
    }

    private function emitOnAfterIdentification($data){
        $event = new SubmissionEvent($this,$data);
        $this->getDispatcher()->dispatch(self::ON_AFTER_IDENTIFICATION,$event);
    }
}