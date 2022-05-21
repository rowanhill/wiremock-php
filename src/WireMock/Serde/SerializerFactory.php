<?php

namespace WireMock\Serde;

class SerializerFactory
{
    public static function default(): Serializer
    {
        // Load and unserialize the pre-generated SerdeType lookup
        $lookupSerialized = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'lookup');
        $lookup = unserialize($lookupSerialized);

        return new Serializer($lookup);
    }
}