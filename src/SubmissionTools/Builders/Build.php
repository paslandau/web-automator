<?php
/**
 * Created by PhpStorm.
 * User: Hirnhamster
 * Date: 23.09.2014
 * Time: 10:46
 */

namespace paslandau\WebAutomator\SubmissionTools\Builders;


class Build
{

    public static function requestInfoBuilder()
    {
        return RequestInfoBuilderBuilder::init();
    }

    /**
     * @return ExtractorUseMethod
     */
    public static function extractor()
    {
        return ExtractorBuilder::init();
    }

    public static function extractorData($data = null)
    {
        return DataExtractorBuilder::init($data);
    }

    public static function extractorMulti(array $extractors = null)
    {
        return MultiExtractorBuilder::init($extractors);
    }

    /**
     * @return EvaluatorSetExtractor
     */
    public static function evaluator()
    {
        return EvaluatorBuilder::init();
    }

    public static function booleanExpression()
    {
        return BooleanExpressionBuilder::init();
    }

    /**
     * @param $id
     * @return StepIdentifyBy
     */
    public static function step($id)
    {
        return StepBuilder::init($id);
    }

    /**
     * @param $id
     * @return FinalStepIdentifyBy
     */
    public static function stepFinal($id)
    {
        return FinalStepBuilder::init($id);
    }

    /**
     * @param $id
     * @return ErrorStepIdentifyBy
     */
    public static function stepError($id)
    {
        return ErrorStepBuilder::init($id);
    }

    /**
     * @return SubmissionStepQueueStartAtInterface
     */
    public static function stepQueue()
    {
        return SubmissionStepQueueBuilder::init();
    }

    /**
     * @return SimpleRequestInfoBuilderMethodInterface
     */
    public static function initialRequest()
    {
        return SimpleRequestInfoBuilder::init();
    }

    /**
     * @return DefaultRequestInfoBuilderConvertResponseToInterface
     */
    public static function defaultRequest()
    {
        return SimpleRequestInfoBuilder::init();
    }
} 