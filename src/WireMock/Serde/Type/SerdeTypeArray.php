<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

abstract class SerdeTypeArray extends SerdeType
{
    /**
     * @throws SerializationException
     */
    abstract function denormalizeFromArray(array &$data, Serializer $serializer, array $path): array;

    public function canDenormalize($data): bool
    {
        return is_array($data);
    }

    /**
     * @throws SerializationException
     */
    function denormalize(&$data, Serializer $serializer, array $path): array
    {
        if (!$this->canDenormalize($data)) {
            throw new SerializationException('Cannot denormalize to ' . $this->displayName() .
                ' from data of type ' . gettype($data));
        }
        return $this->denormalizeFromArray($data, $serializer, $path);
    }
}