<?php declare(strict_types=1);

namespace WireMock\Serde;

use ReflectionException;
use ReflectionMethod;
use const PHP_VERSION_ID;

class MethodFactory {
    /**
     * @param string $fqMethodName
     * @throws ReflectionException
     */
    public static function createMethod($fqMethodName): ReflectionMethod {
        return PHP_VERSION_ID >= 80400
            ? ReflectionMethod::createFromMethodName($fqMethodName)
            : new ReflectionMethod($fqMethodName);
    }
}