<?php

namespace WireMock\Serde\Type;

abstract class SerdeTypeSingle extends SerdeType
{
    /** @var string */
    public $typeString;

    /**
     * @param bool $isNullable
     * @param $typeString string
     */
    public function __construct(bool $isNullable, string $typeString)
    {
        parent::__construct($isNullable);
        $this->typeString = $typeString;
    }
}