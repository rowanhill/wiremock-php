<?php

namespace WireMock\Client;

use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;

class JsonValueMatchingStrategy extends ValueMatchingStrategy implements ObjectToPopulateFactoryInterface
{
    private $_ignoreArrayOrder = null;
    private $_ignoreExtraElements = null;

    public function __construct($matchingValue, $ignoreArrayOrder = null, $ignoreExtraElements = null)
    {
        parent::__construct('equalToJson', $matchingValue);
        $this->_ignoreArrayOrder = $ignoreArrayOrder;
        $this->_ignoreExtraElements = $ignoreExtraElements;
    }

    public function toArray()
    {
        $array = parent::toArray();
        if ($this->_ignoreArrayOrder) {
            $array['ignoreArrayOrder'] = $this->_ignoreArrayOrder;
        }
        if ($this->_ignoreExtraElements) {
            $array['ignoreExtraElements'] = $this->_ignoreExtraElements;
        }
        return $array;
    }

    public static function fromArray(array $array)
    {
        $matchingValue = $array['equalToJson'];
        $ignoreArrayOrder = isset($array['ignoreArrayOrder']) && $array['ignoreArrayOrder'];
        $ignoreExtraElements = isset($array['ignoreExtraElements']) && $array['ignoreExtraElements'];
        return new self($matchingValue, $ignoreArrayOrder, $ignoreExtraElements);
    }

    public static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        unset($normalisedArray['matchingType']); // equalToJson
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingValue']);
        return new ObjectToPopulateResult(new self($matchingValue), $normalisedArray);
    }
}
