<?php
/**
 * Created by PhpStorm.
 * User: Hirnhamster
 * Date: 29.09.2014
 * Time: 14:31
 */

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\BooleanExpressions\ExpressionInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\ErrorStep;

/**
 * Class ErrorStepBuilder
 * @method StepBuilder identifyBy(ExpressionInterface $expression)
 */
class ErrorStepBuilder extends AbstractBaseSubmissionStepBuilder implements ErrorStepBuilderBuildOrderInterface{

    public static function init($id){
        return new self($id);
    }

    public function build(){
        $step = new ErrorStep($this->identifier);
        return $step;
    }
} 