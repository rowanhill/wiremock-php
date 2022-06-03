<?php

namespace WireMock\Serde;

class SerdeClassDefinition
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
     * @throws SerializationException
     */
    public function getCatchAllProp(): ?SerdeProp
    {
        $matchingProps = array_filter($this->properties, function($prop) {
            return $prop->catchAll;
        });
        if (count($matchingProps) === 0) {
            return null;
        } elseif (count($matchingProps) === 1) {
            return current($matchingProps);
        } else {
            throw new SerializationException("Expected 0 or 1 prop to be marked @serde-catch-all but found multiple");
        }
    }

    /**
     * @return SerdeProp[]
     */
    public function getAllPropertiesAndArgs(): array
    {
        return $this->properties;
    }

    public function isPossibleSerializedName(string $name): bool
    {
        foreach ($this->properties as $prop) {
            foreach ($prop->getPossibleSerializedNames() as $possibleName) {
                if ($name === $possibleName) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @throws SerializationException
     */
    public function isPhpName(string $name): bool
    {
        return $this->getPropertyByPhpName($name) !== null;
    }
}