<?php

namespace WireMock\Serde;

interface PreDenormalizationAmenderInterface
{
    public static function amendPreDenormalisation(array $normalisedArray): array;
}