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

    public function addSerdeType(string $type, SerdeType $serdeType)
    {
        $this->lookup[$type] = $serdeType;
    }

    public function contains(string $type): bool
    {
        return isset($this->lookup[$type]);
    }
}