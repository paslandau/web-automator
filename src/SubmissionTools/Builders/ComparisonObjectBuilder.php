<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\ComparisonUtility\ComparisonObject;
use paslandau\ComparisonUtility\EqualityComperator;
use paslandau\ComparisonUtility\NumberComperator;
use paslandau\ComparisonUtility\StringComperator;

class ComparisonObjectBuilder {

    private $comparisonObject;

    private function __construct(){
    }

    public static function init()
    {
        return new self();
    }

    public function isTrue()
    {
        $this->setBooleanComperator(EqualityComperator::COMPARE_FUNCTION_IDENTITY, true);
    }

    private function setBooleanComperator($function, $boolean)
    {
        $comp = new EqualityComperator($function);
        $this->comparisonObject = new ComparisonObject($comp, $boolean);
    }

    public function isFalse()
    {
        $this->setBooleanComperator(EqualityComperator::COMPARE_FUNCTION_IDENTITY, false);
    }

    public function matches($pattern, $ignoreCase = false, $canBeNull = false)
    {
        $this->setStringComperator(StringComperator::COMPARE_FUNCTION_MATCHES, $pattern, $ignoreCase, $canBeNull);
    }

    private function setStringComperator($function, $str, $ignoreCase, $canBeNull)
    {
        $comp = new StringComperator($function, $ignoreCase, $canBeNull);
        $this->comparisonObject = new ComparisonObject($comp, $str);
    }

    public function startsWith($str, $ignoreCase = false, $canBeNull = false)
    {
        $this->setStringComperator(StringComperator::COMPARE_FUNCTION_STARTS_WITH, $str, $ignoreCase, $canBeNull);
    }

    public function endsWith($str, $ignoreCase = false, $canBeNull = false)
    {
        $this->setStringComperator(StringComperator::COMPARE_FUNCTION_ENDS_WITH, $str, $ignoreCase, $canBeNull);
    }

    public function contains($str, $ignoreCase = false, $canBeNull = false)
    {
        $this->setStringComperator(StringComperator::COMPARE_FUNCTION_CONTAINS, $str, $ignoreCase, $canBeNull);
    }

    public function isLowerThan($num)
    {
        $this->setNumberComperator(NumberComperator::COMPARE_FUNCTION_LOWER, $num);
    }

    private function setNumberComperator($function, $num)
    {
        $comp = new NumberComperator($function);
        $this->comparisonObject = new ComparisonObject($comp, $num);
    }

    public function isLowerEquals($num)
    {
        $this->setNumberComperator(NumberComperator::COMPARE_FUNCTION_LOWER_EQUALS, $num);
    }

    public function equals($obj, $ignoreCase = false, $canBeNull = false)
    {
        if (is_numeric($obj)) {
            $this->setNumberComperator(NumberComperator::COMPARE_FUNCTION_EQUALS, $obj);
        } else {
            $this->setStringComperator(StringComperator::COMPARE_FUNCTION_EQUALS, $obj, $ignoreCase, $canBeNull);
        }
    }

    public function isGreaterEquals($num)
    {
        $this->setNumberComperator(NumberComperator::COMPARE_FUNCTION_GREATER_EQUALS, $num);
    }

    public function isGreaterThan($num)
    {
        $this->setNumberComperator(NumberComperator::COMPARE_FUNCTION_GREATER, $num);
    }

    public function build(){
        return $this->comparisonObject;
    }
} 