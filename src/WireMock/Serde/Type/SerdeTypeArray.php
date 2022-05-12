<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

abstract class SerdeTypeArray extends SerdeType
{
    /**
     * @throws SerializationException
     */
    abstract function denormalizeFromArray(array &$data, Serializer $serializer): array;

    /**
     * @throws SerializationException
     */
    function denormalize(&$data, Serializer $serializer): array
    {
        if (!is_array($data)) {
            throw new SerializationException('Cannot denormalize to ' . $this->displayName() .
                ' from data of type ' . gettype($data));
        }
        return $this->denormalizeFromArray($data, $serializer);
    }
}