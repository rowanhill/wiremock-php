<?php

namespace WireMock\Serde;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerFactory
{
    public static function default()
    {
        return new Serializer(
            [
                new ArrayDenormalizer(),
                new PrePostAmendingNormalizer(
                    new EmptyArrayIgnoringNormalizer(
                        null,
                        new PrivatePropertyNameConverter(),
                        new PhpDocExtractor()
                    )
                )
            ],
            [new JsonEncoder()]
        );
    }
}