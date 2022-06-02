<?php

namespace WireMock\Client;

class JsonPathValueMatchingStrategy extends ValueMatchingStrategy
{
    /**
     * @var string|AdvancedPathPattern
     * @serde-named-by matchingType
     * @serde-possible-names matchingValueNames
     */
    protected $matchingValue;

    /**
     * @param string|AdvancedPathPattern $matchingValue
     */
    public function __construct($matchingValue)
    {
        parent::__construct('matchesJsonPath', $matchingValue);
    }
}