<?php

namespace WireMock\Serde;

interface PostNormalizationAmenderInterface
{
    public static function amendNormalisation(array $normalisedArray, $object): array;
}