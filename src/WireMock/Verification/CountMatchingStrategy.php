<?php

namespace WireMock\Verification;

class CountMatchingStrategy
{
    private $closure;
    private $operator;
    private $operand;

    /**
     * CountMatchingStrategy constructor.
     * @param callable $closure Closure that takes an int and returns a boolean
     * @param string $operator
     * @param int $operand
     */
    private function __construct($closure, $operator, $operand)
    {
        $this->closure = $closure;
        $this->operator = $operator;
        $this->operand = $operand;
    }

    /**
     * @param int $count
     * @return boolean
     */
    public function matches($count)
    {
        $closure = $this->closure;
        return $closure($count);
    }

    public function describe()
    {
        return $this->operator . ' ' . $this->operand;
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