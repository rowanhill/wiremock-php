<?php

namespace WireMock\Client;

class LogicalOperatorMatchingStrategy extends ValueMatchingStrategy
{
    /**
     * @param string $matchingType
     * @param ValueMatchingStrategy[] $matchers
     */
    public function __construct(string $matchingType, array $matchers)
    {
        parent::__construct($matchingType, $matchers);
    }

    public function toArray(): array
    {
        return array(
            $this->_matchingType => array_map(function($matcher) {
                return $matcher->toArray();
            }, $this->_matchingValue)
        );
    }

    /**
     * @param array $array
     * @param string $operator
     * @return LogicalOperatorMatchingStrategy
     */
    public static function fromArrayForOperator(array $array, $operator)
    {
        $matchers = array_map(function($matcherArray) {
            return ValueMatchingStrategy::fromArray($matcherArray);
        }, $array[$operator]);
        return new self($operator, $matchers);
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