<?php

namespace WireMock\Serde;

use ReflectionException;
use stdClass;
use WireMock\Serde\Type\SerdeType;
use WireMock\Serde\Type\SerdeTypeAssocArray;
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
     * @param ?SerdeType $serdeType the SerdeType of the class property being normalized (if applicable)
     * @return mixed An associative array or a primitive type
     * @throws ReflectionException
     * @throws SerializationException
     */
    public function normalize($object, bool $isRoot = false, $serdeType = null)
    {
        if (is_object($object)) {
            $type = get_class($object);
            if ($isRoot === true && $this->serdeTypeLookup->isRootType($type) === false) {
                fwrite(STDERR, "Warning: serializing from $type, but this is not a root type\n");
            }
            /** @var ?SerdeTypeClass $serdeType */
            $serdeType = $this->serdeTypeLookup->getSerdeTypeIfExits($type);
            if ($serdeType) {
                $result = $serdeType->normalize($object, $this);
            } else {
                // There's no SerdeType for this class, so we just return it as is, and let json_decode deal with it
                // This allows users to supply objects of unregistered types, perhaps as values within an untyped array,
                // and have them serialized (in a way they can control with JsonSerializable)
                return $object;
            }
        } elseif (is_array($object)) {
            $result = array_map(
                function($value) { return $this->normalize($value); },
                $object
            );

            if ($serdeType instanceof SerdeTypeAssocArray && empty($object)) {
                // We want empty associative arrays to be serialize as "{}" rather than "[]", so we use an empty stdClass
                // object rather than an empty PHP array as the normalized value
                $result = new stdClass();
            }
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
        return $serdeType->denormalize($data, []);
    }
}