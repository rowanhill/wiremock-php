<?php

namespace WireMock\Matching;

use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PostNormalizationAmenderInterface;

class UrlMatchingStrategy implements PostNormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    /** @var string */
    private $matchingType;
    /** @var string */
    private $matchingValue;

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
