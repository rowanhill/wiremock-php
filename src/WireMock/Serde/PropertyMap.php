<?php

namespace WireMock\Serde;

class PropertyMap
{
    /** @var SerdeProp[] */
    private $constructorArgProps;
    /** @var SerdeProp[] keyed by name */
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

    public function getProperty(string $name): ?SerdeProp
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        } else {
            return null;
        }
    }
}