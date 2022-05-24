<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\ArrayMapUtils;
use WireMock\Serde\Serializer;

class SerdeTypeAssocArray extends SerdeTypeArray
{
    /** @var SerdeTypePrimitive */
    public $keyType;
    /** @var SerdeType */
    public $valueType;

    /**
     * @param SerdeTypePrimitive $keyType
     * @param SerdeType $valueType
     */
    public function __construct(SerdeTypePrimitive $keyType, SerdeType $valueType)
    {
        $this->keyType = $keyType;
        $this->valueType = $valueType;
    }

    function displayName(): string
    {
        $key = $this->keyType->displayName();
        $value = $this->valueType->displayName();
        return "array<$key, $value>";
    }

    function denormalizeFromArray(array &$data, Serializer $serializer): array
    {
        return ArrayMapUtils::array_map_assoc(
            function($key, $value) use ($serializer) {
                return [
                    $this->keyType->denormalize($key, $serializer),
                    $this->valueType->denormalize($value, $serializer)
                ];
            },
            $data
        );
    }
}