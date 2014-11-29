<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;

use paslandau\DataFiltering\Evaluation\DataEvaluatorInterface;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;

interface EvaluatorBuild
{
    /**
     * @return DataEvaluatorInterface
     */
    public function build();
}

interface EvaluatorSetExtractor{
    /**
     * @param DataExtractorInterface $ex
     * @return EvaluatorSetComparisonObject
     */
    public function extractedDataFrom(DataExtractorInterface $ex);
}

/**
 * Interface EvaluatorSetComparisonObject
 * @package paslandau\WebAutomator\SubmissionTools\Builders
 * @method EvaluatorBuild isFalse();
 * @method EvaluatorBuild isTrue();
 * @method EvaluatorBuild matches($pattern, $ignoreCase = false, $canBeNull = false);
 * @method EvaluatorBuild startsWith($str, $ignoreCase = false, $canBeNull = false);
 * @method EvaluatorBuild endsWith($str, $ignoreCase = false, $canBeNull = false);
 * @method EvaluatorBuild contains($str, $ignoreCase = false, $canBeNull = false);
 * @method EvaluatorBuild isLowerThan($num);
 * @method EvaluatorBuild isLowerEquals($num);
 * @method EvaluatorBuild equals($obj, $ignoreCase = false, $canBeNull = false);
 * @method EvaluatorBuild isGreaterEquals($num);
 * @method EvaluatorBuild isGreaterThan($num);
 */
interface EvaluatorSetComparisonObject{
}

interface EvaluatorBuilderBuildOrderInterface extends EvaluatorBuild, EvaluatorSetExtractor, EvaluatorSetComparisonObject
{

} 