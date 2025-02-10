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
        if (PHP_VERSION_ID >= 80300) {
            $refMethod = ReflectionMethod::createFromMethodName($fqMethodName);
        } else {
            $refMethod = new ReflectionMethod($fqMethodName);
        }
        if (!$refMethod->isStatic()) {
            throw new SerializationException("$fqMethodName must be a static method but is not");
        }
        $numRequiredParams = $refMethod->getNumberOfRequiredParameters();
        if ($numRequiredParams > 0) {
            throw new SerializationException("$fqMethodName must take no required args, but requires $numRequiredParams");
        }
    }
}