<?php

namespace WireMock\Client;

use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\NormalizerUtils;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;

class EqualToMatchingStrategy extends ValueMatchingStrategy implements PostNormalizationAmenderInterface, PreDenormalizationAmenderInterface, ObjectToPopulateFactoryInterface
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

    /**
     * @param array $array
     * @return EqualToMatchingStrategy
     */
    public static function fromArray(array $array)
    {
        $matchingValue = $array['equalTo'];
        $ignoreCase = isset($array['caseInsensitive']) && $array['caseInsensitive'];
        return new self($matchingValue, $ignoreCase);
    }

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        $normalisedArray = parent::amendPostNormalisation($normalisedArray, $object);

        NormalizerUtils::renameKey($normalisedArray, 'ignoreCase', 'caseInsensitive');

        return $normalisedArray;
    }

    public static function amendPreNormalisation(array $normalisedArray): array
    {
        NormalizerUtils::renameKey($normalisedArray, 'caseInsensitive', 'ignoreCase');
        return $normalisedArray;
    }

    public static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        unset($normalisedArray['matchingType']); //equalTo
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingValue']);
        return new ObjectToPopulateResult(new EqualToMatchingStrategy($matchingValue), $normalisedArray);
    }
}