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
    /** @var bool */
    public $unwrapped;

    /**
     * @param string $name
     * @param string $owningClassName
     * @param SerdeType $serdeType
     * @param string|null $serializedName
     * @param bool $unwrapped
     */
    public function __construct(
        string $name,
        string $owningClassName,
        SerdeType $serdeType,
        string $serializedName = null,
        bool $unwrapped = false
    )
    {
        $this->name = $name;
        $this->owningClassName = $owningClassName;
        $this->serdeType = $serdeType;
        $this->serializedName = $serializedName;
        $this->unwrapped = $unwrapped;
    }

    /**
     * @throws SerializationException
     */
    public function instantiateAndConsumeData(array &$data, Serializer $serializer, array $path)
    {
        $path[] = $this->name;
        if (!$this->unwrapped) {
            $name = $this->getSerializedName();
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

    public function getSerializedName(): string
    {
        return $this->serializedName !== null ? $this->serializedName : $this->name;
    }
}