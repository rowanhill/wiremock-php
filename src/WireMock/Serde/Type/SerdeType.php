<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;

abstract class SerdeType
{
    abstract function displayName(): string;

    abstract function canDenormalize($data): bool;

    /**
     * @throws SerializationException
     */
    abstract function denormalize(&$data, array $path);
}