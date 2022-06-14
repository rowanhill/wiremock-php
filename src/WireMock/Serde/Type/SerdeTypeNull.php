<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;

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

    function denormalize(&$data, array $path)
    {
        if (!$this->canDenormalize($data)) {
            throw new SerializationException('Cannot denormalize non-null data to null');
        }
        return null;
    }
}