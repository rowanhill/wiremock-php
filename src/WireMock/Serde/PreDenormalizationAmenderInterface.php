<?php

namespace WireMock\Serde;

interface PreDenormalizationAmenderInterface
{
    public static function amendPreNormalisation(array $normalisedArray): array;
}