<?php

namespace WireMock\Client;

class EqualToMatchingStrategy extends ValueMatchingStrategy
{
    private $_ignoreCase = false;

    public function __construct($matchingValue, $ignoreCase = false)
    {
        parent::__construct('equalTo', $matchingValue);
        $this->_ignoreCase = $ignoreCase;
    }

    public function toArray()
    {
        $array = parent::toArray();
        if ($this->_ignoreCase) {
            $array['caseInsensitive'] = true;
        }
        return $array;
    }
}