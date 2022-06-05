<?php

namespace WireMock\Serde;

class CanonicalNameUtils
{
    public static function stripLeadingBackslashIfNeeded(string $str): string
    {
        if (substr($str, 0, 1) === '\\') {
            return substr($str, 1);
        } else {
            return $str;
        }
    }
}