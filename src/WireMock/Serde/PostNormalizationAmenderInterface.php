<?php

namespace WireMock\Serde;

interface PostNormalizationAmenderInterface
{
    public static function amendPostNormalisation(array $normalisedArray, $object): array;
}