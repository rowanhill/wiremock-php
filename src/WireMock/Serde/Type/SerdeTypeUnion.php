<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

class SerdeTypeUnion extends SerdeTypeSingle
{
    /** @var SerdeTypePrimitive[] */
    private $primitiveSerdeTypes;
    /** @var SerdeTypeClass|SerdeTypeArray|null */
    private $classOrArraySerdeType;

    /**
     * @param bool $isNullable
     * @param string $typeString
     * @param SerdeTypePrimitive[] $primitiveSerdeTypes
     * @param SerdeTypeArray|SerdeTypeClass|null $classOrArraySerdeType
     */
    public function __construct(bool $isNullable, string $typeString, array $primitiveSerdeTypes, $classOrArraySerdeType)
    {
        parent::__construct($isNullable, $typeString);
        $this->primitiveSerdeTypes = $primitiveSerdeTypes;
        $this->classOrArraySerdeType = $classOrArraySerdeType;
    }

    function displayName(): string
    {
        return $this->typeString;
    }

    /**
     * @throws \ReflectionException
     * @throws SerializationException
     */
    function denormalize(&$data, Serializer $serializer)
    {
        if (is_array($data)) {
            if ($this->classOrArraySerdeType === null) {
                throw new SerializationException("Array data given when denormalizing union type, but no non-primitive type exists: $this->typeString");
            }
            return $this->classOrArraySerdeType->denormalize($data, $serializer);
        } else {
            if (empty($this->primitiveSerdeTypes)) {
                throw new SerializationException("Non-array data given when denormalizing union type, but no primitive types exists: $this->typeString");
            }
            return $this->primitiveSerdeTypes[0]->denormalize($data, $serializer);
        }
    }

}