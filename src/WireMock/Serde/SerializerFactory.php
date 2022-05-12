<?php

namespace WireMock\Serde;

class SerializerFactory
{
    public static function default()
    {
        return new \WireMock\Serde\Serializer();
    }
}