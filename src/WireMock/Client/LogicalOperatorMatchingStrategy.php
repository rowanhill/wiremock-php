<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class LogicalOperatorMatchingStrategy extends ValueMatchingStrategy implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /**
     * @param string $matchingType
     * @param ValueMatchingStrategy[] $matchers
     */
    public function __construct(string $matchingType, array $matchers)
    {
        parent::__construct($matchingType, $matchers);
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