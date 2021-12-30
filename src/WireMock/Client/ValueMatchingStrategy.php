<?php

namespace WireMock\Client;

class ValueMatchingStrategy
{
    /** @var string */
    protected $_matchingType;
    /** @var string|boolean|ValueMatchingStrategy[] */
    protected $_matchingValue;

    public function __construct($matchingType, $matchingValue)
    {
        $this->_matchingType = $matchingType;
        $this->_matchingValue = $matchingValue;
    }

    public function toArray()
    {
        return array($this->_matchingType => $this->_matchingValue);
    }

    public function and(ValueMatchingStrategy $other)
    {
        return LogicalOperatorMatchingStrategy::andAll($this, $other);
    }

    public function or(ValueMatchingStrategy $other)
    {
        return LogicalOperatorMatchingStrategy::orAll($this, $other);
    }
}
