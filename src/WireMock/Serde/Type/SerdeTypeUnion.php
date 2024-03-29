<?php

namespace WireMock\Serde\Type;

use ReflectionException;
use WireMock\Serde\SerializationException;

class SerdeTypeUnion extends SerdeType
{
    /** @var SerdeTypePrimitive[] */
    private $primitiveSerdeTypes;
    /** @var SerdeTypeClass|SerdeTypeArray|null */
    private $classOrArraySerdeType;

    /**
     * @param SerdeTypePrimitive[] $primitiveSerdeTypes
     * @param SerdeTypeArray|SerdeTypeClass|null $classOrArraySerdeType
     */
    public function __construct(array $primitiveSerdeTypes, $classOrArraySerdeType)
    {
        $this->primitiveSerdeTypes = $primitiveSerdeTypes;
        $this->classOrArraySerdeType = $classOrArraySerdeType;
    }

    static function setSubtypes(SerdeTypeUnion $serdeType, array $primitiveSerdeTypes, $classOrArraySerdeType)
    {
        $serdeType->primitiveSerdeTypes = $primitiveSerdeTypes;
        $serdeType->classOrArraySerdeType = $classOrArraySerdeType;
    }

    function displayName(): string
    {
        $types = $this->primitiveSerdeTypes;
        if ($this->classOrArraySerdeType !== null) {
            $types[] = $this->classOrArraySerdeType;
        }
        $typeDisplayNames = array_map(function($t) { return '('.$t->displayName().')'; }, $types);
        return join('|', $typeDisplayNames);
    }

    function canDenormalize($data): bool
    {
        if ($this->classOrArraySerdeType !== null && $this->classOrArraySerdeType->canDenormalize($data)) {
            return true;
        }
        foreach ($this->primitiveSerdeTypes as $serdeType) {
            if ($serdeType->canDenormalize($data)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @throws ReflectionException
     * @throws SerializationException
     */
    function denormalize(&$data, array $path)
    {
        if ($this->classOrArraySerdeType !== null && $this->classOrArraySerdeType->canDenormalize($data)) {
            return $this->classOrArraySerdeType->denormalize($data, $path);
        }

        foreach ($this->primitiveSerdeTypes as $serdeType) {
            if ($serdeType->canDenormalize($data)) {
                return $serdeType->denormalize($data, $path);
            }
        }

        $dataType = gettype($data);
        $targetType = $this->displayName();
        throw new SerializationException("Cannot denormalize data of type $dataType to $targetType");
    }

    /**
     * @return bool Whether the union represents either a nullable class type (and nothing more)
     */
    function isNullableClass(): bool
    {
        return $this->classOrArraySerdeType instanceof SerdeTypeClass &&
            count($this->primitiveSerdeTypes) === 1 &&
            $this->primitiveSerdeTypes[0] instanceof SerdeTypeNull;
    }

    /**
     * @return bool Whether the union represents either a nullable class type (and nothing more)
     */
    function isNullableArray(): bool
    {
        return $this->classOrArraySerdeType instanceof SerdeTypeArray &&
            count($this->primitiveSerdeTypes) === 1 &&
            $this->primitiveSerdeTypes[0] instanceof SerdeTypeNull;
    }

    /**
     * @throws SerializationException
     */
    function getClassTypeOrThrow(): SerdeTypeClass
    {
        if (!($this->classOrArraySerdeType instanceof SerdeTypeClass)) {
            throw new SerializationException('Expected a SerdeTypeClass but was ' . get_class($this->classOrArraySerdeType));
        }
        return $this->classOrArraySerdeType;
    }
}