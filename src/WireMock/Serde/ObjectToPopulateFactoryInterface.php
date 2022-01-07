<?php

namespace WireMock\Serde;

use Symfony\Component\Serializer\Serializer;

interface ObjectToPopulateFactoryInterface
{
    static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult;
}