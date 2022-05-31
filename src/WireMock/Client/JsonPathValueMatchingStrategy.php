<?php

namespace WireMock\Client;

use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;

class JsonPathValueMatchingStrategy extends ValueMatchingStrategy implements PostNormalizationAmenderInterface, PreDenormalizationAmenderInterface
{
    /** @var ValueMatchingStrategy */
    private $valueMatchingStrategy;

    /**
     * @param string $matchingValue
     * @param ?ValueMatchingStrategy $valueMatchingStrategy
     */
    public function __construct(string $matchingValue, ?ValueMatchingStrategy $valueMatchingStrategy = null)
    {
        parent::__construct('matchesJsonPath', $matchingValue);
        $this->valueMatchingStrategy = $valueMatchingStrategy;
    }

    // TODO: allow $matchingValue to be string|AdvancedPathPattern (cf WireMock class) with a @serde-unwrapped annotation
    // TODO: might need to allow @serde-unwrapped on primitives (and just ignore)?
    // (Can also do the same for XPathValueMatchingStrategy)
    /*
     * Native:
     * {
     *   "matchingType": "matchesJsonPath",
     *   "matchingValue: "some-path",
     *   "valueMatchingStrategy": { [vms] },
     * }
     *
     * Parent amend:
     * {
     *   "valueMatchingStrategy": { [vms] },
     *   "matchesJsonPath": "some-path"
     * }
     *
     * Child amend:
     * {
     *   "matchesJsonPath": {
     *     [vms],
     *     "expression": "some-path"
     *   }
     * }
     */
    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        $normalisedArray = parent::amendPostNormalisation($normalisedArray, $object);
        if (isset($normalisedArray['valueMatchingStrategy'])) {
            $strategy = $normalisedArray['valueMatchingStrategy'];
            unset($normalisedArray['valueMatchingStrategy']);
            $jsonPathExpression = $normalisedArray['matchesJsonPath'];
            $strategy['expression'] = $jsonPathExpression;
            $normalisedArray['matchesJsonPath'] = $strategy;
        }
        return $normalisedArray;
    }

    public static function amendPreDenormalisation(array $normalisedArray): array
    {
        $normalisedArray = parent::amendPreDenormalisation($normalisedArray);
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingValue']);
        if (is_array($matchingValue)) {
            $jsonPath = $matchingValue['expression'];
            unset($matchingValue['expression']);
            $normalisedArray['valueMatchingStrategy'] = $matchingValue;
        } else {
            $jsonPath = $matchingValue;
        }
        $normalisedArray['matchingValue'] = $jsonPath;
        return $normalisedArray;
    }

    /**
     * @return ValueMatchingStrategy
     */
    public function getValueMatchingStrategy(): ?ValueMatchingStrategy
    {
        return $this->valueMatchingStrategy;
    }
}