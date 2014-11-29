<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\DataFiltering\Evaluation\DataEvaluator;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;

/**
 * Class EvaluatorBuilder
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
class EvaluatorBuilder implements EvaluatorBuilderBuildOrderInterface
{

    /**
     * @var DataExtractorInterface
     */
    private $extractor;

    /**
     * @var ComparisonObjectBuilder
     */
    private $comparisonObjectBuilder;

    public static function init()
    {
        $comparisonObjectBuilder = ComparisonObjectBuilder::init();
        return new self($comparisonObjectBuilder);
    }

    private function __construct($comparisonObjectBuilder)
    {
        $this->comparisonObjectBuilder = $comparisonObjectBuilder;
    }

    public function __call($name, $arguments)
    {
        // check method in child objects
        $children = [
            $this->comparisonObjectBuilder
        ];
        foreach($children as $child){
            if(method_exists($child, $name)){
                call_user_func_array([$child,$name],$arguments);
                return $this;
            }
        }
        throw new \InvalidArgumentException("Method '$name' is unknown!");
    }

    public function extractedDataFrom(DataExtractorInterface $ex){
        $this->extractor = $ex;
        return $this;
    }

    public function build()
    {
        $comparisonObject = $this->comparisonObjectBuilder->build();
        return new DataEvaluator($comparisonObject, $this->extractor);
    }

    #region Stupid copy and paste because PHP doesn_t recognize __call as fullfilling an interface (even if @method is declared in the class doc comment)
    // @see https://bugs.php.net/bug.php?id=41162


    #endregion
} 