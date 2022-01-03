<?php

namespace WireMock\Client;

use WireMock\Serde\NormalizerUtils;
use WireMock\Serde\PostNormalizationAmenderInterface;

class EqualToMatchingStrategy extends ValueMatchingStrategy implements PostNormalizationAmenderInterface
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

    public static function amendNormalisation(array $normalisedArray, $object): array
    {
        $normalisedArray = parent::amendNormalisation($normalisedArray, $object);

        NormalizerUtils::renameKey($normalisedArray, 'ignoreCase', 'caseInsensitive');

        return $normalisedArray;
    }
}