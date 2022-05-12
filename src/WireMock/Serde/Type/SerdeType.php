<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

abstract class SerdeType
{
    /** @var bool */
    private $isNullable;

    /**
     * @param bool $isNullable
     */
    public function __construct(bool $isNullable)
    {
        $this->isNullable = $isNullable;
    }

    /**
     * @return boolean
     */
    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }

    abstract function displayName(): string;

    /**
     * @throws SerializationException
     */
    abstract function denormalize(&$data, Serializer $serializer);
}