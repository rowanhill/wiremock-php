<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\PropertyMapCache;
use WireMock\Serde\SerializationException;

class SerdeTypeFactory
{
    /**
     * @throws SerializationException
     */
    public function parseTypeString(string $type, PropertyMapCache $propertyMapCache): SerdeType
    {
        $isNullable = false;

        // Eat nullability by union
        // TODO: null not at end of type union? regex replace?
        if (substr($type, -5) === '|null') {
            $type = substr($type, 0, -5);
            $isNullable = true;
        } else if (substr($type, -7) === ' | null') {
            $type = substr($type, 0, -7);
            $isNullable = true;
        }
        // TODO: nullability by ? (in other cases)

        if (
            $type === 'bool' || $type === 'boolean' ||
            $type === 'int' || $type === 'integer' ||
            $type === 'float' || $type === 'double' ||
            $type === 'string'
        ) {
            return new SerdeTypePrimitive($isNullable, $type);
        } elseif ($type === 'array') {
            return new SerdeTypeUntypedArray($isNullable);
        } elseif (strpos($type, '|') !== false) {
            $unionTypes = array_map(function($t) use ($propertyMapCache) { return $this->parseTypeString($t, $propertyMapCache); }, preg_split('/\|/', $type));
            $primitives = [];
            $nonPrimitive = null;
            foreach ($unionTypes as $t) {
                if ($t instanceof SerdeTypePrimitive) {
                    $primitives[] = $t;
                } elseif (($t instanceof SerdeTypeClass) || ($t instanceof SerdeTypeArray)) {
                    if ($nonPrimitive !== null) {
                        throw new SerializationException("Serde of union types with more than one non-primitive type are not supported: $type");
                    } else {
                        $nonPrimitive = $t;
                    }
                } else {
                    throw new SerializationException("Serde of union types only supported for primitives, classes, and arrays: $type");
                }
            }
            return new SerdeTypeUnion($isNullable, $type, $primitives, $nonPrimitive);
        } elseif (substr($type, -2) === '[]') {
            $elementTypeString = substr($type, 0, -2);
            $elementType = $this->parseTypeString($elementTypeString, $propertyMapCache);
            return new SerdeTypeTypedArray($isNullable, $elementType);
        } else {
            $matches = array();
            if (preg_match('/array<\s*([^,]+?)\s*,\s*([^>]+?)\s*>/', $type, $matches) === 1) {
                $key = $this->parseTypeString($matches[1], $propertyMapCache);
                $value = $this->parseTypeString($matches[2], $propertyMapCache);
                if (!($key instanceof SerdeTypePrimitive)) {
                    throw new SerializationException("Unexpected key type of associative array: $type");
                }
                return new SerdeTypeAssocArray($isNullable, $key, $value);
            } else {
                if (substr($type, 0, 1) === '?') {
                    $isNullable = true;
                    $type = substr($type, 1);
                }
                $fqn = $propertyMapCache->getFullyQualifiedName($type);
                if ($fqn !== null) {
                    return new SerdeTypeClass($isNullable, $fqn, $propertyMapCache);
                } else {
                    throw new SerializationException("Tried to parse unexpected type: $type");
                }
            }
        }
    }
}