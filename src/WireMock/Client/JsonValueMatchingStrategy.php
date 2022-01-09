<?php

namespace WireMock\Client;

class JsonValueMatchingStrategy extends ValueMatchingStrategy
{
    private $ignoreArrayOrder = null;
    private $ignoreExtraElements = null;

    public function __construct($matchingValue, $ignoreArrayOrder = null, $ignoreExtraElements = null)
    {
        parent::__construct('equalToJson', $matchingValue);
        $this->ignoreArrayOrder = $ignoreArrayOrder;
        $this->ignoreExtraElements = $ignoreExtraElements;
    }
}
