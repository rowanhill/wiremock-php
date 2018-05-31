<?php

namespace WireMock\Client;

class JsonPathMatchingStrategy extends ValueMatchingStrategy
{
    public function __construct($jsonPath, ValueMatchingStrategy $matchingStrategy)
    {
        $body = $jsonPath;
        if ($matchingStrategy) {
            $body = $matchingStrategy->toArray();
            $body['expression'] = $jsonPath;
        }

        parent::__construct('matchesJsonPath', $body);
    }
}
