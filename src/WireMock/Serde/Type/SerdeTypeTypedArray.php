<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

class SerdeTypeTypedArray extends SerdeTypeArray
{
    /** @var SerdeType */
    private $type;

    /**
     * @param SerdeType $type
     */
    public function __construct(SerdeType $type)
    {
        $this->type = $type;
    }

    static function setInnerType(SerdeTypeTypedArray $serdeType, SerdeType $type)
    {
        $serdeType->type = $type;
    }

    public function displayName(): string
    {
        return $this->type->displayName() . '[]';
    }

    /**
     * @throws SerializationException
     */
    function denormalizeFromArray(array &$data, Serializer $serializer, array $path): array
    {
        $result = [];
        foreach ($data as $index => $element) {
            $newPath = $path;
            $newPath[] = "[$index]";
            $result[$index] = $this->type->denormalize($element, $serializer, $newPath);
        }
        return $result;
    }
}