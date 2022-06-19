<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;

abstract class SerdeTypeArray extends SerdeType
{
    /**
     * @throws SerializationException
     */
    abstract function denormalizeFromArray(array &$data, array $path): array;

    public function canDenormalize($data): bool
    {
        return is_array($data);
    }

    /**
     * @throws SerializationException
     */
    function denormalize(&$data, array $path): array
    {
        if (!$this->canDenormalize($data)) {
            throw new SerializationException('Cannot denormalize to ' . $this->displayName() .
                ' from data of type ' . gettype($data) . ' at path ' . join('.', $path));
        }
        return $this->denormalizeFromArray($data, $path);
    }
}