<?php

namespace WireMock\Serde;

class CanonicalNameUtils
{
    public static function prependBackslashIfNeeded(string $str): string
    {
        if (substr($str, 0, 1) === '\\') {
            return $str;
        } else {
            return '\\'.$str;
        }
    }
}