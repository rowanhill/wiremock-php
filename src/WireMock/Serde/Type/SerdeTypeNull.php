<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

class SerdeTypeNull extends SerdeTypePrimitive
{
    public function __construct()
    {
        parent::__construct('null');
    }

    function canDenormalize($data): bool
    {
        return $data === null;
    }

    function denormalize(&$data, Serializer $serializer)
    {
        if (!$this->canDenormalize($data)) {
            throw new SerializationException('Cannot deserialize non-null data to null');
        }
        return null;
    }
}