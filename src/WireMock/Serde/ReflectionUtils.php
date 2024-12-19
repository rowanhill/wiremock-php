<?php

namespace WireMock\Serde;


use ReflectionMethod;

final class ReflectionUtils
{
    public static function reflectMethod(string $methodName): ReflectionMethod
    {
        if (PHP_VERSION_ID >= 80300) {
            $refMethod = ReflectionMethod::createFromMethodName($methodName);
        } else {
            $refMethod = new ReflectionMethod($methodName);
        }

        return $refMethod;
    }
}
