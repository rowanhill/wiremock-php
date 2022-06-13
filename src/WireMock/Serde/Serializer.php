<?php

namespace WireMock\Serde;

use ReflectionException;
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
     * @throws ReflectionException|SerializationException
     */
    public function serialize($object): string
    {
        $normalizedArray = $this->normalize($object, true);
        return json_encode($normalizedArray, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param mixed $object object to normalize
     * @param bool $isRoot whether this is the root object being normalized
     * @return mixed An associative array or a primitive type
     * @throws ReflectionException
     * @throws SerializationException
     */
    public function normalize($object, bool $isRoot = false)
    {
        if (is_object($object)) {
            $type = get_class($object);
            /** @var SerdeTypeClass $serdeType */
            $serdeType = $this->serdeTypeLookup->getSerdeType($type);
            if ($isRoot === true && $this->serdeTypeLookup->isRootType($type) === false) {
                fwrite(STDERR, "Warning: serializing from $type, but this is not a root type\n");
            }
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
        return $this->denormalize($data, $type, true);
    }

    /**
     * @throws SerializationException
     */
    public function denormalize(&$data, string $type, bool $isRoot = false)
    {
        $serdeType = $this->serdeTypeLookup->getSerdeType($type);
        if ($isRoot === true && $this->serdeTypeLookup->isRootType($type) === false) {
            fwrite(STDERR, "Warning: deserializing to $type, but this is not a root type\n");
        }
        return $serdeType->denormalize($data, $this, []);
    }
}