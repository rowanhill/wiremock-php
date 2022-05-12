<?php

namespace WireMock\Client;

class EqualToMatchingStrategy extends ValueMatchingStrategy
{
    /** @var bool */
    private $caseInsensitive = false;

    public function __construct($matchingValue, $caseInsensitive = false)
    {
        parent::__construct('equalTo', $matchingValue);
        $this->caseInsensitive = $caseInsensitive;
    }

    /**
     * @return bool
     */
    public function isCaseInsensitive()
    {
        return $this->caseInsensitive;
    }
}