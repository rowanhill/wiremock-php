<?php

namespace WireMock\Client;

class LogicalOperatorMatchingStrategy extends ValueMatchingStrategy
{
    /**
     * @param string $matchingType
     * @param ValueMatchingStrategy[] $matchingValue
     */
    public function __construct(string $matchingType, array $matchingValue)
    {
        parent::__construct($matchingType, $matchingValue);
    }

    /**
     * @param ValueMatchingStrategy $matchers
     * @return LogicalOperatorMatchingStrategy
     */
    public static function andAll(...$matchers): LogicalOperatorMatchingStrategy
    {
        return new self("and", $matchers);
    }

    /**
     * @param ValueMatchingStrategy $matchers
     * @return LogicalOperatorMatchingStrategy
     */
    public static function orAll(...$matchers): LogicalOperatorMatchingStrategy
    {
        return new self("or", $matchers);
    }
}