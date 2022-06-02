<?php

namespace WireMock\Matching;

use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\Serializer;

class UrlMatchingStrategy implements ObjectToPopulateFactoryInterface
{
    /** @var string */
    private $matchingType;
    /**
     * @var string
     * @serde-named-by matchingType
     * @serde-possible-names matchingValueNames
     */
    private $matchingValue;

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function matchingValueNames(): array
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

    /*
     * Object creation here follows the standard, automatic pattern _except_ if the required keys are missing it returns
     * null, rather than throwing an exception
     */
    static function createObjectToPopulate(array $normalisedArray, Serializer $serializer): ObjectToPopulateResult
    {
        $strategy = null;
        if (array_key_exists('matchingType', $normalisedArray)) {
            $matchingType = $normalisedArray['matchingType'];
            $matchingValue = $normalisedArray['matchingValue'];
            unset($normalisedArray['matchingType']);
            unset($normalisedArray['matchingValue']);
            $strategy = new UrlMatchingStrategy($matchingType, $matchingValue);
        }
        return new ObjectToPopulateResult($strategy, $normalisedArray);
    }
}
