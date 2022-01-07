<?php

namespace WireMock\Client;

use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PostNormalizationAmenderInterface;

class JsonPathValueMatchingStrategy extends ValueMatchingStrategy implements PostNormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    /** @var ValueMatchingStrategy */
    private $_valueMatchingStrategy;

    /**
     * @param string $jsonPath
     * @param ValueMatchingStrategy $valueMatchingStrategy
     */
    public function __construct($jsonPath, $valueMatchingStrategy = null)
    {
        parent::__construct('matchesJsonPath', $jsonPath);
        $this->_valueMatchingStrategy = $valueMatchingStrategy;
    }

    public function toArray()
    {
        if (!$this->_valueMatchingStrategy) {
            return parent::toArray();
        } else {
            return array(
                'matchesJsonPath' => array_merge(
                    array(
                        'expression' => $this->_matchingValue
                    ),
                    $this->_valueMatchingStrategy->toArray()
                )
            );
        }
    }

    public static function fromArray(array $array)
    {
        if (is_array($array['matchesJsonPath'])) {
            $matchingValue = $array['matchesJsonPath']['expression'];
            $matchingStrategyArray = $array['matchesJsonPath'];
            unset($matchingStrategyArray['expression']);
            $matchingStrategy = ValueMatchingStrategy::fromArray($matchingStrategyArray);
            return new self($matchingValue, $matchingStrategy);
        } else {
            $matchingValue = $array['matchesJsonPath'];
            return new self($matchingValue);
        }
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

    public static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        unset($normalisedArray['matchingType']); // matchesJsonPath
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingValue']);
        if (is_array($matchingValue)) {
            $jsonPath = $matchingValue['expression'];
            unset($matchingValue['expression']);
            $normalisedArray['valueMatchingStrategy'] = $matchingValue;
        } else {
            $jsonPath = $matchingValue;
        }
        return new ObjectToPopulateResult(new self($jsonPath), $normalisedArray);
    }
}