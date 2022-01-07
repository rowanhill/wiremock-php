<?php

namespace WireMock\Serde;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Serializer;

trait DummyConstructorArgsObjectToPopulateFactory
{
    static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        $class = new \ReflectionClass(static::class);
        $constructor = $class->getConstructor();
        if ($constructor == null) {
            throw new NotNormalizableValueException("Could not create object to populate: no constructor");
        }
        $params = $constructor->getParameters();
        $params = array_slice($params, 0, $constructor->getNumberOfRequiredParameters());
        $paramValues = array_map(function($param) use (&$normalisedArray, $serializer, $format, $context) {
            if ($param->allowsNull()) {
                return null;
            } else {
                switch ($typeName = $param->getType()->getName()) {
                    case 'string':
                        return 'dummy';
                    case 'float':
                        return 0.0;
                    case 'int':
                        return 0;
                    case 'bool':
                        return false;
                    case 'array':
                        return [];
                    default:
                        $paramName = $param->getName();
                        if (isset($normalisedArray[$paramName]) &&
                            $serializer->supportsDenormalization($normalisedArray[$paramName], $typeName, $format, $context)) {
                            $paramNormalisedArray = $normalisedArray[$paramName];
                            unset($normalisedArray[$paramName]);
                            return $serializer->denormalize($paramNormalisedArray, $typeName, $format, $context);
                        }
                        throw new NotNormalizableValueException(sprintf(
                            "Cannot invoke constructor of %s because %s is not nullable, has no default value, and cannot be denormalized",
                            static::class,
                            $paramName
                        ));
                }
            }
        }, $params);
        $object = $class->newInstance(...$paramValues);
        return new ObjectToPopulateResult($object, $normalisedArray);
    }
}