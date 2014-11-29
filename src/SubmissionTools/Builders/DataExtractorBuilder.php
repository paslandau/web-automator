<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;

use paslandau\DataFiltering\Extraction\InputDataExtractor;

class DataExtractorBuilder extends AbstractBaseExtractorBuilder {
    private $data;

    private function __construct($data = null)
    {
        $this->data = $data;
    }

    public static function init($data = null)
    {
        return new self($data);
    }

    public function data($data = null)
    {
        $this->data = $data;
        return $this;
    }

    public function build(){
        $this->base = new InputDataExtractor($this->data);
        return parent::build();
    }
} 