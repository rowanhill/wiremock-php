<?php

namespace WireMock\Serde;

class ArrayMapUtils
{
    /**
     * @param callable $f Called with ($key, $value). Should return [newKey, newValue]
     * @param array $a
     * @return array
     */
    public static function array_map_assoc(callable $f, array $a): array
    {
        return array_column(array_map($f, array_keys($a), $a), 1, 0);
    }
}