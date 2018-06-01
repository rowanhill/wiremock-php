<?php

namespace WireMock\Client;

class JsonValueMatchingStrategy extends ValueMatchingStrategy
{
    private $_ignoreArrayOrder;
    private $_ignoreExtraElements;

    public function __construct($matchingValue, $ignoreArrayOrder = true, $ignoreExtraElements = true)
    {
        parent::__construct('equalToJson', $matchingValue);
        $this->_ignoreArrayOrder = $ignoreArrayOrder;
        $this->_ignoreExtraElements = $ignoreExtraElements;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['ignoreArrayOrder'] = $this->_ignoreArrayOrder;
        $array['ignoreExtraElements'] = $this->_ignoreExtraElements;
        return $array;
    }
}
