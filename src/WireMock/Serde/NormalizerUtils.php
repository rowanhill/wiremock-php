<?php

namespace WireMock\Serde;

class NormalizerUtils
{
    public static function renameKey(array &$array, string $oldKey, string $newKey)
    {
        if (isset($array[$oldKey])) {
            $array[$newKey] = $array[$oldKey];
            unset($array[$oldKey]);
        }
    }

    public static function inline(array &$normalisedArray, string $key)
    {
        if (isset($normalisedArray[$key])) {
            $urlMatchingStrategyArray = $normalisedArray[$key];
            unset($normalisedArray[$key]);
            $normalisedArray = array_merge($normalisedArray, $urlMatchingStrategyArray);
        }
    }
}