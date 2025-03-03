<?php declare(strict_types=1);

namespace WireMock\Serde;

use ReflectionException;
use ReflectionMethod;
use const PHP_VERSION_ID;

class MethodFactory {
    /**
     * @throws ReflectionException
     */
    public static function createMethod($fqMethodName) {
        return PHP_VERSION_ID >= 80400
            ? ReflectionMethod::createFromMethodName($fqMethodName)
            : new ReflectionMethod($fqMethodName);
    }
}