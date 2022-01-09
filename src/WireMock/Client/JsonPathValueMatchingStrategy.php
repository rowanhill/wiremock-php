<?php

namespace WireMock\Client;

use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\ObjectToPopulateResult;
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
}