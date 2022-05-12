<?php

namespace WireMock\Serde;

interface ObjectToPopulateFactoryInterface
{
    static function createObjectToPopulate(array $normalisedArray, Serializer $serializer): ObjectToPopulateResult;
}