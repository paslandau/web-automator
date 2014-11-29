<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\BooleanExpressions\And_;
use paslandau\BooleanExpressions\ExpressionInterface;
use paslandau\BooleanExpressions\Not_;
use paslandau\BooleanExpressions\Or_;

class BooleanExpressionBuilder {
    /**
     * @var mixed[]
     */
    private $stack;

    private function __construct(){
        $this->stack = array();
    }

    public static function init(){
        return new self();
    }

    public function _and_($expression_s = null){
        $this->and_($expression_s);
        $this->_and();
        return $this;
    }

    public function _or_($expression_s = null){
        $this->or_($expression_s);
        $this->_or();
        return $this;
    }

    public function _not_($expression_s = null){
        $this->not_($expression_s);
        $this->_not();
        return $this;
    }

    public function and_($expression_s = null){
        return $this->group("and", $expression_s);
    }

    /**
     * Closing
     * @return BooleanExpressionBuilder
     */
    public function _and(){
        return $this->endGroup("and");
    }

    public function or_($expression_s = null){
        return $this->group("or", $expression_s);
    }

    /**
     * Closing
     * @return BooleanExpressionBuilder
     */
    public function _or(){
        return $this->endGroup("or");
    }

    public function not_($expression = null){
        return $this->group("not", $expression);
    }
    public function _not(){
        return $this->endGroup("not");
    }

    private function group($expr, $expression_s){
        $this->stack[] = $expr;
        if($expression_s === null){
           return $this;
        }
        if(!is_array($expression_s)){
            $expression_s = [$expression_s];
        }
        foreach($expression_s as $exp){
            $this->exp($exp);
        }
        return $this;
    }

    public function exp(ExpressionInterface $expr){
        $this->stack[] = $expr;
        return $this;
    }

    private function endGroup($op = null){
        $this->stack[] = "end";
        return $this;
    }

    public function build(){
        $rev = array_reverse($this->stack);
        $kellerGroups = [];
        $group = [];
        while( ($next = array_shift($rev)) !== null){
            if( $next == "end"){
                $kellerGroups[] = $group;
                $group = [];
            }elseif(is_string($next)){
                $exp = null;
                $group = array_reverse($group);
                switch($next){
                    case "and": $exp = new And_($group); break;
                    case "not": $exp = new Not_(array_shift($group)); break;
                    case "or": $exp = new Or_($group); break;
                    default: throw new \RuntimeException("Unknown operator '$next'");
                }
                $group = array_pop($kellerGroups);
                if($group === null){
                    break;
                }
                $group[] = $exp;
            }else{
                $group[] = $next;
            }
        }
        if(count($group) != 1){
            throw new \RuntimeException("Group count is '".count($group)."', expected count of '1'");
        }
        return array_shift($group);
    }
} 