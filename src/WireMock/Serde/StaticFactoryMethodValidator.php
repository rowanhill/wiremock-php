<?php

namespace WireMock\Serde;

use ReflectionException;

class StaticFactoryMethodValidator
{
    /**
     * @param string $fqMethodName
     * @throws ReflectionException
     * @throws SerializationException
     */
    public static function validate($fqMethodName): void {
        $refMethod = MethodFactory::createMethod($fqMethodName);
        if (!$refMethod->isStatic()) {
            throw new SerializationException("$fqMethodName must be a static method but is not");
        }
        $numRequiredParams = $refMethod->getNumberOfRequiredParameters();
        if ($numRequiredParams > 0) {
            throw new SerializationException("$fqMethodName must take no required args, but requires $numRequiredParams");
        }
    }
}