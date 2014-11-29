<?php
/**
 * Created by PhpStorm.
 * User: Hirnhamster
 * Date: 29.09.2014
 * Time: 14:36
 */

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\BooleanExpressions\ExpressionInterface;
use paslandau\DataFiltering\Identification\BaseIdentifier;

abstract class AbstractBaseSubmissionStepBuilder {
    protected $id;
    protected $identifier;

    protected function __construct($id){
        $this->id = $id;
    }

    public function identifyBy(ExpressionInterface $expression){
        $this->identifier = new BaseIdentifier($expression,$this->id);
        return $this;
    }
} 