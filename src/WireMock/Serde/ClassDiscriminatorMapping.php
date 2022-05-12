<?php

namespace WireMock\Serde;

class ClassDiscriminatorMapping
{
    /** @var string */
    private $discriminatorPropName;
    /** @var string[] keyed by values of discriminator prop */
    private $discriminatorValueToClassMap;

    /**
     * @param string $discriminatorPropName
     * @param string[] $discriminatorValueToClassMap
     */
    public function __construct(string $discriminatorPropName, array $discriminatorValueToClassMap)
    {
        $this->discriminatorPropName = $discriminatorPropName;
        $this->discriminatorValueToClassMap = $discriminatorValueToClassMap;
    }

    /**
     * @return string
     */
    public function getDiscriminatorPropName(): string
    {
        return $this->discriminatorPropName;
    }

    /**
     * @return string[]
     */
    public function getDiscriminatorValueToClassMap(): array
    {
        return $this->discriminatorValueToClassMap;
    }
}