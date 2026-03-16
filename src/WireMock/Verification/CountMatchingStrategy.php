<?php

namespace WireMock\Verification;

class CountMatchingStrategy
{
    /** @var callable(int): bool */
    private $closure;
    /** @var string */
    private $operator;
    /** @var int */
    private $operand;

    /**
     * CountMatchingStrategy constructor.
     * @param callable(int): bool $closure Closure that takes an int and returns a boolean
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
     * @return bool
     */
    public function matches($count)
    {
        $closure = $this->closure;
        return $closure($count);
    }

    public function describe(): string
    {
        return $this->operator . ' ' . $this->operand;
    }

    /** @param int $expected */
    public static function lessThan($expected): self
    {
        return new self(function($actual) use ($expected) { return $actual < $expected; }, '<', $expected);
    }

    /** @param int $expected */
    public static function lessThanOrExactly($expected): self
    {
        return new self(function($actual) use ($expected) { return $actual <= $expected; }, '<=', $expected);
    }

    /** @param int $expected */
    public static function exactly($expected): self
    {
        return new self(function($actual) use ($expected) { return $actual == $expected; }, '==', $expected);
    }

    /** @param int $expected */
    public static function moreThanOrExactly($expected): self
    {
        return new self(function($actual) use ($expected) { return $actual >= $expected; }, '>=', $expected);
    }

    /** @param int $expected */
    public static function moreThan($expected): self
    {
        return new self(function($actual) use ($expected) { return $actual > $expected; }, '>', $expected);
    }
}