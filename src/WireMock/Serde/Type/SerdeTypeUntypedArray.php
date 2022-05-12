<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\Serializer;

class SerdeTypeUntypedArray extends SerdeTypeArray
{
    function displayName(): string
    {
        return 'array';
    }

    function denormalizeFromArray(array &$data, Serializer $serializer): array
    {
        return $data;
    }
}