<?php

namespace WireMock\Client;

class AdvancedPathPattern
{
    /**
     * @var ValueMatchingStrategy
     * @serde-unwrapped
     */
    private $subMatchingStrategy;
    /** @var string */
    private $expression;

    /**
     * @param ValueMatchingStrategy $subMatchingStrategy
     * @param string $expression
     */
    public function __construct(ValueMatchingStrategy $subMatchingStrategy, string $expression)
    {
        $this->subMatchingStrategy = $subMatchingStrategy;
        $this->expression = $expression;
    }

    /**
     * @return ValueMatchingStrategy
     */
    public function getSubMatchingStrategy(): ValueMatchingStrategy
    {
        return $this->subMatchingStrategy;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }
}