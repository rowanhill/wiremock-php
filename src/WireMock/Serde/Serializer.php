<?php

namespace WireMock\Serde;

use ReflectionException;
use ReflectionMethod;
use WireMock\Serde\Type\SerdeType;
use WireMock\Serde\Type\SerdeTypeClass;
use WireMock\Serde\Type\SerdeTypeLookup;

class Serializer
{
    /** @var SerdeTypeLookup */
    private $serdeTypeLookup;

    public function __construct(SerdeTypeLookup $serdeTypeLookup)
    {
        $this->serdeTypeLookup = $serdeTypeLookup;
    }

    /**
     * Serializes data object into JSON
     *
     * @param mixed $object object to serialize
     * @return string JSON serialization of object
     * @throws ReflectionException
     */
    public function serialize($object): string
    {
        $normalizedArray = $this->normalize($object);
        return json_encode($normalizedArray, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param mixed $object object to normalize
     * @return mixed An associative array or a primitive type
     * @throws ReflectionException
     * @throws SerializationException
     */
    public function normalize($object)
    {
        if (is_object($object)) {
            $type = get_class($object);
            /** @var SerdeTypeClass $serdeType */
            $serdeType = $this->getSerdeType($type);
            $result = $serdeType->normalize($object, $this);
        } elseif (is_array($object)) {
            $result = ArrayMapUtils::array_map_assoc(
                function($key, $value) { return [$key, $this->normalize($value)]; },
                $object
            );
        } else {
            $result = $object;
        }

        return $result;
    }

    /**
     * Deserializes JSON into data object of the given type
     *
     * @param string $json
     * @param string $type
     *
     * @return mixed
     * @throws SerializationException
     */
    public function deserialize(string $json, string $type)
    {
        $data = json_decode($json, true);
        return $this->denormalize($data, $type);
    }

    /**
     * @throws SerializationException
     */
    public function denormalize(&$data, string $type)
    {
        $serdeType = $this->getSerdeType($type);
        return $serdeType->denormalize($data, $this, []);
    }

    /**
     * @throws SerializationException
     */
    public function getSerdeType(string $type): SerdeType
    {
        return $this->serdeTypeLookup->getSerdeType($type);
    }
}