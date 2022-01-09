<?php

namespace WireMock\Client;

class EqualToMatchingStrategy extends ValueMatchingStrategy
{
    private $caseInsensitive = false;

    public function __construct($matchingValue, $caseInsensitive = false)
    {
        parent::__construct('equalTo', $matchingValue);
        $this->caseInsensitive = $caseInsensitive;
    }
}