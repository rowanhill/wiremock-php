<?php

namespace WireMock\Serde;

class NormalizerUtils
{
    public static function inline(array &$normalisedArray, string $key)
    {
        if (isset($normalisedArray[$key])) {
            $urlMatchingStrategyArray = $normalisedArray[$key];
            unset($normalisedArray[$key]);
            $normalisedArray = array_merge($normalisedArray, $urlMatchingStrategyArray);
        }
    }
}