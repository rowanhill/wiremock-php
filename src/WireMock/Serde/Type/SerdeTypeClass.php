<?php

namespace WireMock\Serde\Type;

use ReflectionClass;
use ReflectionException;
use WireMock\Serde\ClassDiscriminator;
use WireMock\Serde\MappingProvider;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PreDenormalizationAmenderInterface;
use WireMock\Serde\PropertyMap;
use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

class SerdeTypeClass extends SerdeTypeSingle
{
    /** @var PropertyMap */
    private $propertyMap;

    public function __construct(bool $isNullable, string $typeString, PropertyMap $propertyMap)
    {
        parent::__construct($isNullable, $typeString);
        $this->propertyMap = $propertyMap;
    }

    public function displayName(): string
    {
        return $this->typeString;
    }

    /**
     * @throws SerializationException
     * @throws ReflectionException
     */
    function denormalize(&$data, Serializer $serializer): ?object
    {
        if (!is_array($data)) {
            throw new SerializationException('Cannot denormalize to ' . $this->displayName() .
                ' from data of type ' . gettype($data));
        }
        $discriminatedType = $this->getDiscriminatedType($data, $this->typeString);
        if (!class_exists($discriminatedType)) {
            throw new SerializationException('Cannot denormalize to ' . $this->displayName() .
                " because no class named $discriminatedType exists");
        }
        if (is_subclass_of($discriminatedType, PreDenormalizationAmenderInterface::class)) {
            $data = forward_static_call([$discriminatedType, 'amendPreDenormalisation'], $data);
        }
        $discriminatedSerdeType = $serializer->getSerdeType($discriminatedType);
        if (!($discriminatedSerdeType instanceof SerdeTypeClass)) {
            throw new SerializationException("Discriminated type of $this->typeString was $discriminatedType" .
            ", which was expected to be represented by a SerdeTypeClass, but is actually represented by " .
            get_class($discriminatedSerdeType));
        }
        return $discriminatedSerdeType->instantiate($data, $serializer);
    }

    /**
     * @param $data
     * @param Serializer $serializer
     * @return object|null
     * @throws ReflectionException|SerializationException
     */
    function instantiate(&$data, Serializer $serializer): ?object
    {
        $refClass = new ReflectionClass($this->typeString);
        $object = $this->constructObject($data, $refClass, $serializer);
        if ($object !== null) {
            $this->populateObject($data, $object, $refClass, $serializer);
        }
        return $object;
    }

    private function getDiscriminatedType(&$data, string $type): string
    {
        if (!is_subclass_of($type, MappingProvider::class)) {
            return $type;
        }
        /** @var ClassDiscriminator $classDiscriminator */
        $classDiscriminator = forward_static_call(array($type, 'getDiscriminatorMapping'));
        return $classDiscriminator->getDiscriminatedType($data, $type);
    }

    /**
     * @throws SerializationException
     * @throws ReflectionException
     */
    private function constructObject(array &$data, ReflectionClass $refClass, Serializer $serializer): ?object
    {
        // Delegate to createObjectToPopulate if specified
        if (is_subclass_of($this->typeString, ObjectToPopulateFactoryInterface::class)) {
            /** @var ObjectToPopulateResult $result */
            $result = forward_static_call([$this->typeString, 'createObjectToPopulate'], $data, $serializer);
            if ($result->object == null) {
                return null;
            }
            $data = $result->normalisedArray;
            return $result->object;
        }

        // Otherwise, make the constructor args from the data and then call the constructor
        $args = array_map(
            function($param) use (&$data, $serializer) {
                return $param->instantiateAndConsumeData($data, $serializer);
            },
            $this->propertyMap->getConstructorArgProps()
        );

        return $refClass->newInstanceArgs($args);
    }

    /**
     * @throws SerializationException
     * @throws ReflectionException
     */
    private function populateObject(array &$data, object $object, ReflectionClass $refClass, Serializer $serializer)
    {
        foreach ($data as $propertyName => $propertyData) {
            $serdeProp = $this->propertyMap->getProperty($propertyName);
            if ($serdeProp === null) {
                // Ignore properties from JSON that don't exist on the PHP class
                // (This allows for newer versions of WireMock to add new properties and older versions of wiremock-php
                // to still work okay, for example)
                continue;
            }
            $propertyValue = $serdeProp->instantiateAndConsumeData($data, $serializer);
            $refProp = $refClass->getProperty($serdeProp->name);
            $refProp->setAccessible(true);
            $refProp->setValue($object, $propertyValue);
        }
    }
}