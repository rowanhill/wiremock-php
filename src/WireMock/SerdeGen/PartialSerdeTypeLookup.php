<?php

namespace WireMock\SerdeGen;


use WireMock\Serde\Type\SerdeType;
use WireMock\Serde\Type\SerdeTypeLookup;

class PartialSerdeTypeLookup extends SerdeTypeLookup
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function addSerdeType(string $type, bool $isNullable, SerdeType $serdeType)
    {
        $key = $this->getKey($type, $isNullable);
        $this->lookup[$key] = $serdeType;
    }

    public function contains(string $type, bool $isNullable): bool
    {
        $key = $this->getKey($type, $isNullable);
        return isset($this->lookup[$key]);
    }
}