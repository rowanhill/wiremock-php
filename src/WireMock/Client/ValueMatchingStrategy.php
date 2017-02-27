<?php

namespace WireMock\Client;

class ValueMatchingStrategy
{
    /** @var string */
    private $_matchingType;
    /** @var string */
    private $_matchingValue;

    public function __construct($matchingType, $matchingValue)
    {
        $this->_matchingType = $matchingType;
        $this->_matchingValue = $matchingValue;
    }

    public function toArray()
    {
        return array($this->_matchingType => $this->_matchingValue);
    }
}
