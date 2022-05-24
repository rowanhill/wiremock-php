<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

class SerdeTypeTypedArray extends SerdeTypeArray
{
    /** @var SerdeType */
    private $type;

    /**
     * @param SerdeType $type
     */
    public function __construct(SerdeType $type)
    {
        $this->type = $type;
    }

    public function displayName(): string
    {
        return $this->type->displayName() . '[]';
    }

    /**
     * @throws SerializationException
     */
    function denormalizeFromArray(array &$data, Serializer $serializer): array
    {
        return array_map(
            function($element) use ($serializer) { return $this->type->denormalize($element, $serializer); },
            $data
        );
    }
}