<?php

namespace WireMock\Verification;

class CountMatchingStrategy
{
    private $_closure;
    private $_operator;
    private $_operand;

    /**
     * CountMatchingStrategy constructor.
     * @param callable $closure Closure that takes an int and returns a boolean
     * @param string $operator
     * @param int $operand
     */
    private function __construct($closure, $operator, $operand)
    {
        $this->_closure = $closure;
        $this->_operator = $operator;
        $this->_operand = $operand;
    }

    /**
     * @param int $count
     * @return boolean
     */
    public function matches($count)
    {
        $closure = $this->_closure;
        return $closure($count);
    }

    public function describe()
    {
        return $this->_operator . ' ' . $this->_operand;
    }

    public static function lessThan($expected) {
        return new self(function($actual) use ($expected) { return $actual < $expected; }, '<', $expected);
    }

    public static function lessThanOrExactly($expected) {
        return new self(function($actual) use ($expected) { return $actual <= $expected; }, '<=', $expected);
    }

    public static function exactly($expected) {
        return new self(function($actual) use ($expected) { return $actual == $expected; }, '==', $expected);
    }

    public static function moreThanOrExactly($expected) {
        return new self(function($actual) use ($expected) { return $actual >= $expected; }, '>=', $expected);
    }

    public static function moreThan($expected) {
        return new self(function($actual) use ($expected) { return $actual > $expected; }, '>', $expected);
    }
}