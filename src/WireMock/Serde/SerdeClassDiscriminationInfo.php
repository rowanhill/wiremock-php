<?php

namespace WireMock\Serde;

use ReflectionException;
use ReflectionMethod;

class SerdeClassDiscriminationInfo
{
    /**
     * Fully qualified name of the factory method to produce a ClassDiscriminator for this class
     * @var string
     */
    private $discriminatorFactoryName;

    /**
     * @param string $discriminatorFactoryName
     * @throws SerializationException|ReflectionException
     */
    public function __construct(string $discriminatorFactoryName)
    {
        $this->discriminatorFactoryName = $discriminatorFactoryName;
        StaticFactoryMethodValidator::validate($discriminatorFactoryName);
    }

    /**
     * @throws ReflectionException
     */
    public function getDiscriminator(): ?ClassDiscriminator
    {
        $refMethod = new ReflectionMethod($this->discriminatorFactoryName);
        $refMethod->setAccessible(true);
        return $refMethod->invoke(null);
    }
}