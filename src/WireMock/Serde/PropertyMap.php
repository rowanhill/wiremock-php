<?php

namespace WireMock\Serde;

class PropertyMap
{
    /** @var SerdeProp[] */
    private $constructorArgProps;
    /** @var SerdeProp[] */
    private $properties;

    /**
     * @param SerdeProp[] $constructorArgProps
     * @param SerdeProp[] $properties
     */
    public function __construct(array $constructorArgProps, array $properties)
    {
        $this->constructorArgProps = $constructorArgProps;
        $this->properties = $properties;
    }

    /**
     * @return SerdeProp[]
     */
    public function getConstructorArgProps(): array
    {
        return $this->constructorArgProps;
    }

    public function getPropertyBySerializedName(string $name, array $data): ?SerdeProp
    {
        $matchingProps = array_filter($this->properties, function($prop) use ($name, $data) {
            return $prop->getSerializedName($data) === $name;
        });
        if (count($matchingProps) === 0) {
            return null;
        } elseif (count($matchingProps) === 1) {
            return current($matchingProps);
        } else {
            throw new SerializationException("Expected 0 or 1 prop to match serialized name $name but found multiple");
        }
    }

    public function getPropertyByPhpName(string $name): ?SerdeProp
    {
        $matchingProps = array_filter($this->properties, function($prop) use ($name) {
            return $prop->name === $name;
        });
        if (count($matchingProps) === 0) {
            return null;
        } elseif (count($matchingProps) === 1) {
            return current($matchingProps);
        } else {
            throw new SerializationException("Expected 0 or 1 prop to match serialized name $name but found multiple");
        }
    }

    /**
     * @return SerdeProp[]
     */
    public function getAllPropertiesAndArgs(): array
    {
        return $this->properties;
    }
}