<?php

namespace WireMock\Matching;

class UrlMatchingStrategy
{
    /** @var string */
    private $matchingType;
    /**
     * @var string
     * @serde-named-by matchingType
     * @serde-possible-names matchingValueNames
     */
    private $matchingValue;

    /**
     * @noinspection PhpUnusedPrivateMethodInspection
     * @return list<string>
     */
    private static function matchingValueNames(): array // @phpstan-ignore method.unused
    {
        return ['url', 'urlPattern', 'urlPath', 'urlPathPattern'];
    }

    /**
     * @param string $matchingType
     * @param string $matchingValue
     */
    public function __construct($matchingType, $matchingValue)
    {
        $this->matchingType = $matchingType;
        $this->matchingValue = $matchingValue;
    }

    /**
     * @return string
     */
    public function getMatchingType()
    {
        return $this->matchingType;
    }

    /**
     * @return string
     */
    public function getMatchingValue()
    {
        return $this->matchingValue;
    }
}
