<?php

namespace WireMock\Serde\Type;

abstract class SerdeTypeSingle extends SerdeType
{
    /** @var string */
    public $typeString;

    /**
     * @param $typeString string
     */
    public function __construct(string $typeString)
    {
        $this->typeString = $typeString;
    }

    public function displayName(): string
    {
        return $this->typeString;
    }
}