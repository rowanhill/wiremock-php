<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\Serializer;

class SerdeTypePrimitive extends SerdeTypeSingle
{
    public function displayName(): string
    {
        return $this->typeString;
    }

    function denormalize(&$data, Serializer $serializer)
    {
        return $data;
    }
}