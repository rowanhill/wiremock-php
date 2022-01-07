<?php

namespace WireMock\Matching;

use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PostNormalizationAmenderInterface;

class UrlMatchingStrategy implements PostNormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    /** @var string */
    private $_matchingType;
    /** @var string */
    private $_matchingValue;

    /**
     * @param string $matchingType
     * @param string $matchingValue
     */
    public function __construct($matchingType, $matchingValue)
    {
        $this->_matchingType = $matchingType;
        $this->_matchingValue = $matchingValue;
    }

    /**
     * @return string
     */
    public function getMatchingType()
    {
        return $this->_matchingType;
    }

    /**
     * @return string
     */
    public function getMatchingValue()
    {
        return $this->_matchingValue;
    }

    public function toArray()
    {
        return array(
            $this->_matchingType => $this->_matchingValue,
        );
    }

    /**
     * @param array $array
     * @return UrlMatchingStrategy|null
     */
    public static function fromArray(array $array)
    {
        foreach (array('url', 'urlPattern', 'urlPath', 'urlPathPattern') as $type) {
            if (isset($array[$type])) {
                return new UrlMatchingStrategy($type, $array[$type]);
            }
        }
        return null;
    }

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        $matchingType = $normalisedArray['matchingType'];
        $matchingValue = $normalisedArray['matchingValue'];
        return [$matchingType => $matchingValue];
    }

    static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        $strategy = null;
        foreach (array('url', 'urlPattern', 'urlPath', 'urlPathPattern') as $type) {
            if (isset($normalisedArray[$type])) {
                $matchingValue = $normalisedArray[$type];
                unset($normalisedArray[$type]);
                $strategy = new UrlMatchingStrategy($type, $matchingValue);
                break;
            }
        }
        return new ObjectToPopulateResult($strategy, $normalisedArray);
    }
}
