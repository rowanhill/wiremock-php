<?php

namespace WireMock\Serde;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use WireMock\Serde\Type\SerdeType;

class SerdeProp
{
    /** @var string */
    public $name;
    /** @var string */
    public $owningClassName;
    /** @var SerdeType */
    public $serdeType;
    /** @var string|null */
    private $serializedName;

    /**
     * @param string $name
     * @param string $owningClassName
     * @param SerdeType $serdeType
     * @param string|null $serializedName
     */
    public function __construct(string $name, string $owningClassName, SerdeType $serdeType, string $serializedName = null)
    {
        $this->name = $name;
        $this->owningClassName = $owningClassName;
        $this->serdeType = $serdeType;
        $this->serializedName = $serializedName;
    }

    /**
     * @throws SerializationException
     */
    public function instantiateAndConsumeData(array &$data, Serializer $serializer)
    {
        $name = $this->getSerializedName();
        $propData = array_key_exists($name, $data) ? $data[$name] : null;
        unset($data[$name]);
        return $this->serdeType->denormalize($propData, $serializer);
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

    public function getSerializedName(): string
    {
        return $this->serializedName !== null ? $this->serializedName : $this->name;
    }
}