<?php

namespace WireMock\Client;

class JsonPathValueMatchingStrategy extends ValueMatchingStrategy
{
    /**
     * @param string|AdvancedPathPattern $matchingValue
     */
    public function __construct($matchingValue)
    {
        parent::__construct('matchesJsonPath', $matchingValue);
    }
}