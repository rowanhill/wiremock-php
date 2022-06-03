<?php

namespace WireMock\Serde\Type;

use ReflectionClass;
use ReflectionException;
use WireMock\Serde\ClassDiscriminator;
use WireMock\Serde\MappingProvider;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;
use WireMock\Serde\PropertyMap;
use WireMock\Serde\PropNaming\ConstantPropertyNamingStrategy;
use WireMock\Serde\PropNaming\ReferencingPropertyNamingStrategy;
use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

class SerdeTypeClass extends SerdeTypeSingle
{
    /** @var PropertyMap */
    private $propertyMap;

    public function __construct(string $typeString, PropertyMap $propertyMap)
    {
        parent::__construct($typeString);
        $this->propertyMap = $propertyMap;
    }

    /**
     * @throws ReflectionException|SerializationException
     */
    public function normalize($object, Serializer $serializer): array
    {
        $props = $this->propertyMap->getAllPropertiesAndArgs();
        $simpleNamedProps = [];
        $referenceNamedProps = [];
        foreach ($props as $prop) {
            if ($prop->includeInNormalizedForm !== true) {
                // Skip any props that shouldn't be included in the normalized form
                continue;
            }

            if ($prop->propertyNamingStrategy === null || $prop->propertyNamingStrategy instanceof ConstantPropertyNamingStrategy) {
                $simpleNamedProps[] = $prop;
            } elseif ($prop->propertyNamingStrategy instanceof ReferencingPropertyNamingStrategy) {
                $referenceNamedProps[] = $prop;
            } else {
                throw new SerializationException("Unexpected prop naming strategy of type " .
                    get_class($prop->propertyNamingStrategy));
            }
        }
        $result = [];
        // These props don't care about the data passed in to getSerializedName, which is why we do them first
        foreach ($simpleNamedProps as $prop) {
            $value = $prop->getData($object);
            $normalizedValue = $serializer->normalize($value);
            if ($prop->unwrapped && is_array($normalizedValue)) {
                $result = array_merge($result, $normalizedValue);
            } else {
                $result[$prop->getSerializedName($result)] = $normalizedValue;
            }
        }
        // These props rely on the values of other props to name themselves
        foreach ($referenceNamedProps as $prop) {
            $value = $prop->getData($object);
            $normalizedValue = $serializer->normalize($value);
            if ($prop->unwrapped) {
                throw new SerializationException("Did not expect $prop->name to be both @serde-unwrapped and @serde-named-by");
            }
            /** @var ReferencingPropertyNamingStrategy $namingStrat */
            $namingStrat = $prop->propertyNamingStrategy;
            $namingPropName = $namingStrat->namingPropertyName;
            $result[$prop->getSerializedName($result)] = $normalizedValue;
            unset($result[$namingPropName]);
        }
        if ($object instanceof PostNormalizationAmenderInterface) {
            $result = forward_static_call([get_class($object), 'amendPostNormalisation'], $result, $object);
        }
        foreach ($result as $key => $item) {
            if ((is_array($item) && empty($item)) || is_null($item)) {
                unset($result[$key]);
            }
        }

        return $result;
    }

    public function canDenormalize($data): bool
    {
        return is_array($data);
    }

    /**
     * @throws SerializationException
     * @throws ReflectionException
     */
    public function denormalize(&$data, Serializer $serializer, array $path): ?object
    {
        if (!$this->canDenormalize($data)) {
            throw new SerializationException('Cannot denormalize to ' . $this->displayName() .
                ' from data of type ' . gettype($data));
        }
        $discriminatedType = $this->getDiscriminatedType($data);
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
        return $discriminatedSerdeType->instantiate($data, $serializer, $path);
    }

    /**
     * @param $data
     * @param Serializer $serializer
     * @return object|null
     * @throws ReflectionException|SerializationException
     */
    private function instantiate(&$data, Serializer $serializer, array $path): ?object
    {
        $this->reverseNamedByProps($data);
        $this->reverseNamedProps($data);
        $this->reverseUnwrappedProps($data);
        $object = $this->constructObject($data, $serializer, $path);
        if ($object !== null) {
            $this->populateObject($data, $object, $serializer, $path);
        }
        return $object;
    }

    private function getDiscriminatedType($data): string
    {
        $type = $this->typeString;

        if (!is_subclass_of($type, MappingProvider::class)) {
            return $type;
        }

        $refType = new ReflectionClass($type);
        $refMeth = $refType->getMethod('getDiscriminatorMapping');
        if ($refMeth->getDeclaringClass()->name !== $refType->name) {
            return $type;
        }

        /** @var ClassDiscriminator $classDiscriminator */
        $classDiscriminator = forward_static_call(array($type, 'getDiscriminatorMapping'));
        return $classDiscriminator->getDiscriminatedType($data);
    }

    /**
     * @throws ReflectionException
     */
    private function reverseNamedByProps(array &$data)
    {
        foreach ($this->propertyMap->getAllPropertiesAndArgs() as $prop) {
            $namingStrategy = $prop->propertyNamingStrategy;
            if (!($namingStrategy instanceof ReferencingPropertyNamingStrategy)) {
                continue;
            }
            $possibleNames = $namingStrategy->getPossibleSerializedNames();

            foreach ($possibleNames as $possibleName) {
                if (array_key_exists($possibleName, $data)) {
                    $value = $data[$possibleName];
                    unset($data[$possibleName]);
                    $data[$namingStrategy->namingPropertyName] = $possibleName;
                    $data[$prop->name] = $value;
                    break;
                }
            }
        }
    }

    private function reverseNamedProps(array &$data)
    {
        foreach ($this->propertyMap->getAllPropertiesAndArgs() as $prop) {
            $namingStrategy = $prop->propertyNamingStrategy;
            if (!($namingStrategy instanceof ConstantPropertyNamingStrategy)) {
                continue;
            }
            $serializedName = $namingStrategy->getSerializedName($data);
            if (array_key_exists($serializedName, $data)) {
                $value = $data[$serializedName];
                unset($data[$serializedName]);
                $data[$prop->name] = $value;
            }
        }
    }

    /**
     * @throws SerializationException
     */
    private function reverseUnwrappedProps(array &$data)
    {
        foreach ($this->propertyMap->getAllPropertiesAndArgs() as $prop) {
            if (!$prop->unwrapped) {
                continue;
            }

            // Find the SerdeClassType we want to "rewrap" to (including the case where it's nullable)
            $nestedClassType = $prop->getPotentiallyNullableSerdeTypeClassOrThrow();

            // Pull out all entries in $data that matches a prop on the @serde-unwrapped prop class
            $propMap = $nestedClassType->propertyMap;
            $nestedData = [];
            foreach ($data as $key => $value) {
                if (!$propMap->isPossibleSerializedName($key)) {
                    continue;
                }
                $nestedData[$key] = $value;
                unset($data[$key]);
            }

            // If we found anything, add all those matched props under the @serde-unwrapped prop's name. If we didn't
            // find anything, leave the key missing in $data - this allows for the @serde-unwrapped prop to be null
            if (count($nestedData) > 0) {
                $data[$prop->name] = $nestedData;
            }
        }
    }

    /**
     * @throws SerializationException
     * @throws ReflectionException
     */
    private function constructObject(array &$data, Serializer $serializer, array $path): ?object
    {
        $refClass = new ReflectionClass($this->typeString);
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
            function($param) use (&$data, $serializer, $path) {
                return $param->instantiateAndConsumeData($data, $serializer, $path);
            },
            $this->propertyMap->getConstructorArgProps()
        );

        return $refClass->newInstanceArgs($args);
    }

    /**
     * @throws SerializationException
     * @throws ReflectionException
     */
    private function populateObject(array &$data, object $object, Serializer $serializer, array $path)
    {
        foreach ($data as $propertyName => $propertyData) {
            $serdeProp = $this->propertyMap->getPropertyByPhpName($propertyName);
            if ($serdeProp === null) {
                // Ignore properties from JSON that don't exist on the PHP class
                // (This allows for newer versions of WireMock to add new properties and older versions of wiremock-php
                // to still work okay, for example)
                continue;
            }
            $propertyValue = $serdeProp->instantiateAndConsumeData($data, $serializer, $path);
            $refClass = new ReflectionClass($serdeProp->owningClassName);
            $refProp = $refClass->getProperty($serdeProp->name);
            $refProp->setAccessible(true);
            $refProp->setValue($object, $propertyValue);
        }
    }
}