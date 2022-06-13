<?php

namespace WireMock\Serde\Type;

class SerdeTypeUntypedArray extends SerdeTypeArray
{
    function displayName(): string
    {
        return 'array';
    }

    function denormalizeFromArray(array &$data, array $path): array
    {
        return $data;
    }
}