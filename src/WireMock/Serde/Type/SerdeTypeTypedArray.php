<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;

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
    function denormalizeFromArray(array &$data, array $path): array
    {
        $result = [];
        foreach ($data as $index => $element) {
            $newPath = $path;
            $newPath[] = "[$index]";
            $result[$index] = $this->type->denormalize($element, $newPath);
        }
        return $result;
    }
}