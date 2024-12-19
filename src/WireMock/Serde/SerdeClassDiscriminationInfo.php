<?php

namespace WireMock\Serde;

use ReflectionException;
use ReflectionMethod;
use WireMock\Serde\Type\SerdeTypeClass;

class SerdeClassDiscriminationInfo
{
    /**
     * Fully qualified name of the factory method to produce a ClassDiscriminator for this class
     * @var string
     */
    private $discriminatorFactoryName;
    /**
     * Mapping from fully qualified class name to SerdeTypeClass for possible discriminated subtypes
     * @var array<string, SerdeTypeClass>
     */
    private $possibleSerdeTypes;

    /**
     * @param string $discriminatorFactoryName
     * @param array $possibleSerdeTypes
     * @throws SerializationException|ReflectionException
     */
    public function __construct(string $discriminatorFactoryName, array $possibleSerdeTypes)
    {
        $this->discriminatorFactoryName = $discriminatorFactoryName;
        StaticFactoryMethodValidator::validate($discriminatorFactoryName);
        $this->possibleSerdeTypes = $possibleSerdeTypes;
    }

    /**
     * @throws ReflectionException
     */
    public function getDiscriminator(): ?ClassDiscriminator
    {
        $refMethod = ReflectionUtils::reflectMethod($this->discriminatorFactoryName);
        $refMethod->setAccessible(true);
        return $refMethod->invoke(null);
    }

    /**
     * @throws SerializationException
     */
    public function getTypeByFullyQualifiedClassName(string $fqn): SerdeTypeClass
    {
        if (!array_key_exists($fqn, $this->possibleSerdeTypes)) {
            throw new SerializationException("Unregistered discriminated subtype $fqn");
        }
        return $this->possibleSerdeTypes[$fqn];
    }
}
