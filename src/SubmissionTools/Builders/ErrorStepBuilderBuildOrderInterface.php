<?php
/**
 * Created by PhpStorm.
 * User: Hirnhamster
 * Date: 05.10.2014
 * Time: 17:18
 */

namespace paslandau\WebAutomator\SubmissionTools\Builders;

use paslandau\BooleanExpressions\ExpressionInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\ErrorStepInterface;

interface ErrorStepBuild
{
    /**
     * @return ErrorStepInterface
     */
    public function build();
}

interface ErrorStepIdentifyBy
{
    /**
     * @param ExpressionInterface $expression
     * @return ErrorStepBuild
     */
    public function identifyBy(ExpressionInterface $expression);
}

interface ErrorStepBuilderBuildOrderInterface extends ErrorStepIdentifyBy, ErrorStepBuild
{

} 