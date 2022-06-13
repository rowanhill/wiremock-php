<?php

namespace WireMock\Serde;

use ReflectionException;
use ReflectionProperty;
use WireMock\Serde\PropNaming\PropertyNamingStrategy;
use WireMock\Serde\Type\SerdeType;
use WireMock\Serde\Type\SerdeTypeClass;
use WireMock\Serde\Type\SerdeTypeUnion;

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
    public $catchAll;
    /** @var bool */
    public $includeInNormalizedForm;

    /**
     * @param string $name
     * @param string $owningClassName
     * @param SerdeType $serdeType
     * @param ?PropertyNamingStrategy $propertyNamingStrategy
     * @param bool $unwrapped
     * @param bool $catchAll
     */
    public function __construct(
        string $name,
        string $owningClassName,
        SerdeType $serdeType,
        PropertyNamingStrategy $propertyNamingStrategy = null,
        bool $unwrapped = false,
        bool $catchAll = false
    )
    {
        $this->name = $name;
        $this->owningClassName = $owningClassName;
        $this->serdeType = $serdeType;
        $this->propertyNamingStrategy = $propertyNamingStrategy;
        $this->unwrapped = $unwrapped;
        $this->catchAll = $catchAll;
        $this->includeInNormalizedForm = true;
    }

    /**
     * @throws SerializationException
     */
    public function instantiateAndConsumeData(array &$data, array $path)
    {
        $path[] = $this->name;
        $name = $this->name;
        $propData = array_key_exists($name, $data) ? $data[$name] : null;
        unset($data[$name]);
        return $this->serdeType->denormalize($propData, $path);
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

    public function getPossibleSerializedNames(): array
    {
        if ($this->propertyNamingStrategy !== null) {
            return $this->propertyNamingStrategy->getPossibleSerializedNames();
        } else {
            return [$this->name];
        }
    }

    /**
     * @return SerdeTypeClass
     * @throws SerializationException
     */
    public function getPotentiallyNullableSerdeTypeClassOrThrow(): SerdeTypeClass {
        if ($this->serdeType instanceof SerdeTypeClass) {
            return $this->serdeType;
        } elseif ($this->serdeType instanceof SerdeTypeUnion) {
            if ($this->serdeType->isNullableClass()) {
                return $this->serdeType->getClassTypeOrThrow();
            }
        }
        throw new SerializationException("Cannot get SerdeTypeClass for prop $this->name because it is of type " . $this->serdeType->displayName());
    }
}