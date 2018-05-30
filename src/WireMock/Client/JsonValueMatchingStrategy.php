<?php

namespace WireMock\Client;

class JsonValueMatchingStrategy extends ValueMatchingStrategy
{
    const COMPARE_MODE__NON_EXTENSIBLE = 'NON_EXTENSIBLE';
    const COMPARE_MODE__LENIENT = 'LENIENT';
    const COMPARE_MODE__STRICT = 'STRICT';
    const COMPARE_MODE__STRICT_ORDER = 'STRICT_ORDER';

    private $_jsonCompareMode;

    public function __construct($matchingValue, $jsonCompareMode)
    {
        parent::__construct('equalToJson', $matchingValue);
        $this->_jsonCompareMode = $jsonCompareMode;
    }

    public function toArray()
    {
        $array = parent::toArray();

        switch ($this->_jsonCompareMode) {
            case self::COMPARE_MODE__NON_EXTENSIBLE:
                $array['ignoreArrayOrder'] = true;
                $array['ignoreExtraElements'] = true;
                break;
            case self::COMPARE_MODE__LENIENT:
                $array['ignoreArrayOrder'] = true;
                $array['ignoreExtraElements'] = false;
                break;
            case self::COMPARE_MODE__STRICT:
                $array['ignoreArrayOrder'] = false;
                $array['ignoreExtraElements'] = true;
                break;
            case self::COMPARE_MODE__STRICT_ORDER:
                $array['ignoreArrayOrder'] = false;
                $array['ignoreExtraElements'] = false;
                break;
        }
        return $array;
    }
}
