<?php

namespace WireMock\Serde;

use ReflectionException;
use ReflectionProperty;
use WireMock\Serde\PropNaming\PropertyNamingStrategy;
use WireMock\Serde\Type\SerdeType;

class SerdeProp
{
    /** @var string */
    public $name;
    /** @var string */
    public $owningClassName;
    /** @var SerdeType */
    public $serdeType;
    /** @var PropertyNamingStrategy|null */
    public $propertyNamingStrategy;
    /** @var bool */
    public $unwrapped;
    /** @var bool */
    public $includeInNormalizedForm;

    /**
     * @param string $name
     * @param string $owningClassName
     * @param SerdeType $serdeType
     * @param ?PropertyNamingStrategy $propertyNamingStrategy
     * @param bool $unwrapped
     */
    public function __construct(
        string $name,
        string $owningClassName,
        SerdeType $serdeType,
        PropertyNamingStrategy $propertyNamingStrategy = null,
        bool $unwrapped = false
    )
    {
        $this->name = $name;
        $this->owningClassName = $owningClassName;
        $this->serdeType = $serdeType;
        $this->propertyNamingStrategy = $propertyNamingStrategy;
        $this->unwrapped = $unwrapped;
        $this->includeInNormalizedForm = true;
    }

    /**
     * @throws SerializationException
     */
    public function instantiateAndConsumeData(array &$data, Serializer $serializer, array $path)
    {
        $path[] = $this->name;
        if (!$this->unwrapped) {
            $name = $this->name;
            $propData = array_key_exists($name, $data) ? $data[$name] : null;
            unset($data[$name]);
            return $this->serdeType->denormalize($propData, $serializer, $path);
        } else {
            return $this->serdeType->denormalize($data, $serializer, $path);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function getData(object $object)
    {
        $refProp = new ReflectionProperty($this->owningClassName, $this->name);
        $refProp->setAccessible(true);
        return $refProp->getValue($object);
    }

    public function getSerializedName(array $data): string
    {
        if (!$this->propertyNamingStrategy) {
            return $this->name;
        }
        return $this->propertyNamingStrategy->getSerializedName($data);
    }
}