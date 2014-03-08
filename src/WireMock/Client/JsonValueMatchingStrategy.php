<?php

namespace WireMock\Client;

class JsonValueMatchingStrategy extends ValueMatchingStrategy
{
    const COMPARE_MODE__NON_EXTENSIBLE = "NON_EXTENSIBLE";
    const COMPARE_MODE__LENIENT = "LENIENT";
    const COMPARE_MODE__STRICT = "STRICT";
    const COMPARE_MODE__STRICT_ORDER = "STRICT_ORDER";

    private $_jsonCompareMode;

    function __construct($matchingValue, $jsonCompareMode)
    {
        parent::__construct('equalToJson', $matchingValue);
        $this->_jsonCompareMode = $jsonCompareMode;
    }

    function toArray()
    {
        $array = parent::toArray();
        $array['jsonCompareMode'] = $this->_jsonCompareMode;
        return $array;
    }
} 