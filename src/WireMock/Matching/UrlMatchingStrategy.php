<?php

namespace WireMock\Matching;

class UrlMatchingStrategy
{
    /** @var string */
    private $_matchingType;
    /** @var string */
    private $_matchingValue;

    /**
     * @param string $matchingType
     * @param string $matchingValue
     */
    public function __construct($matchingType, $matchingValue)
    {
        $this->_matchingType = $matchingType;
        $this->_matchingValue = $matchingValue;
    }

    public function toArray()
    {
        return array(
            $this->_matchingType => $this->_matchingValue
        );
    }
}
