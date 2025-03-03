<?php

namespace WireMock\Serde;

use ReflectionException;
use ReflectionMethod;
use const PHP_VERSION_ID;

class StaticFactoryMethodValidator
{
    /**
     * @throws ReflectionException
     * @throws SerializationException
     */
    public static function validate($fqMethodName) {
        $refMethod = StaticFactoryMethodValidator::createMethod($fqMethodName);
        if (!$refMethod->isStatic()) {
            throw new SerializationException("$fqMethodName must be a static method but is not");
        }
        $numRequiredParams = $refMethod->getNumberOfRequiredParameters();
        if ($numRequiredParams > 0) {
            throw new SerializationException("$fqMethodName must take no required args, but requires $numRequiredParams");
        }
    }

    /**
     * @throws ReflectionException
     */
    public static function createMethod($fqMethodName) {
        return PHP_VERSION_ID >= 80400
            ? ReflectionMethod::createFromMethodName($fqMethodName)
            : new ReflectionMethod($fqMethodName);
    }
}