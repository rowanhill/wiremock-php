<?php

namespace WireMock\SerdeGen;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\False_;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\True_;
use ReflectionException;
use WireMock\Serde\SerdeClassDefinition;
use WireMock\Serde\SerializationException;
use WireMock\Serde\Type\SerdeType;
use WireMock\Serde\Type\SerdeTypeArray;
use WireMock\Serde\Type\SerdeTypeAssocArray;
use WireMock\Serde\Type\SerdeTypeClass;
use WireMock\Serde\Type\SerdeTypeNull;
use WireMock\Serde\Type\SerdeTypePrimitive;
use WireMock\Serde\Type\SerdeTypeTypedArray;
use WireMock\Serde\Type\SerdeTypeUnion;
use WireMock\Serde\Type\SerdeTypeUntypedArray;

class SerdeTypeParser
{
    /** @var PartialSerdeTypeLookup */
    private $partialSerdeTypeLookup;

    /**
     * @param PartialSerdeTypeLookup $partialSerdeTypeLookup
     */
    public function __construct(PartialSerdeTypeLookup $partialSerdeTypeLookup)
    {
        $this->partialSerdeTypeLookup = $partialSerdeTypeLookup;
    }

    /**
     * @throws SerializationException|ReflectionException
     */
    public function parseTypeString(string $type): SerdeType
    {
        $typeResolver = new TypeResolver();
        $resolvedType = $typeResolver->resolve($type);

        return $this->resolveTypeToSerdeType($resolvedType, true);
    }

    /**
     * Translate from a phpDocumentor Type to a wiremock-php SerdeType
     * @throws SerializationException
     * @throws ReflectionException
     */
    public function resolveTypeToSerdeType(Type $type, bool $forceRootType = false): SerdeType
    {
        $isRootType = $forceRootType || $this->partialSerdeTypeLookup->isRootType($type->__toString());
        if ($type instanceof Array_) {
            $typeString = $type->__toString();
            if ($typeString === 'array') {
                return $this->partialSerdeTypeLookup->addSerdeTypeIfNeeded(
                    'array',
                    new SerdeTypeUntypedArray(),
                    $isRootType
                );
            } else {
                return $this->createSerdeTypeTypedOrAssocArrayIfNeeded($typeString, $isRootType, $type);
            }
        } elseif ($type instanceof Compound) {
            return $this->createSerdeTypeUnionIfNeeded($type, $isRootType);
        } elseif ($type instanceof Nullable) {
            return $this->createNullableSerdeTypeIfNeeded($type, $isRootType);
        } elseif ($type instanceof Object_) {
            return $this->createSerdeTypeClassIfNeeded($type, $isRootType);
        } elseif ($type instanceof Null_) {
            return $this->partialSerdeTypeLookup->addSerdeTypeIfNeeded(
                'null',
                new SerdeTypeNull(),
                $isRootType
            );
        } else {
            // $type is a primitive, or unsupported
            if ($type instanceof Boolean) {
                // Only booleans are currently supported, not explicit false and true
                if ($type instanceof False_ || $type instanceof True_) {
                    throw new SerializationException('Unsupported type ' . get_class($type) . ": $type");
                }
                $primitiveType = 'bool';
            } elseif ($type instanceof Float_) {
                $primitiveType = 'float';
            } elseif ($type instanceof Integer) {
                $primitiveType = 'int';
            } elseif ($type instanceof String_) {
                $primitiveType = 'string';
            } else {
                throw new SerializationException('Unsupported type ' . get_class($type) . ": $type");
            }
            return $this->partialSerdeTypeLookup->addSerdeTypeIfNeeded(
                $primitiveType,
                new SerdeTypePrimitive($primitiveType),
                $isRootType
            );
        }
    }

    /**
     * @throws ReflectionException|SerializationException
     */
    private function createSerdeTypeTypedOrAssocArrayIfNeeded(string $typeString, bool $isRootType, Array_ $type): SerdeType
    {
        if (!$this->partialSerdeTypeLookup->contains($typeString)) {
            if (substr($typeString, -2) === '[]') {
                $this->createSerdeTypeTypedArray($typeString, $isRootType, $type);
            } else {
                $this->createSerdeTypeAssocArray($typeString, $isRootType, $type);
            }
        }
        return $this->partialSerdeTypeLookup->getSerdeType($typeString);
    }

    /**
     * @throws ReflectionException|SerializationException
     */
    private function createSerdeTypeTypedArray(string $typeString, bool $isRootType, Array_ $type): void
    {
        // Create array type with placeholder type, to break cycles
        $arrayType = new SerdeTypeTypedArray(new SerdeTypeNull());
        $this->partialSerdeTypeLookup->addSerdeTypeIfNeeded($typeString, $arrayType, $isRootType);

        // Resolve the real type of the array elements
        // When array of a type is a root type, the type of the array's elements is still considered a root type
        $valueSerdeType = $this->resolveTypeToSerdeType($type->getValueType(), $isRootType);

        // Update the placeholder
        SerdeTypeTypedArray::setInnerType($arrayType, $valueSerdeType);
    }

    /**
     * @throws ReflectionException|SerializationException
     */
    private function createSerdeTypeAssocArray(string $typeString, bool $isRootType, Array_ $type): void
    {
        // Create array type with placeholder key/value types, to break cycles
        $arrayType = new SerdeTypeAssocArray(new SerdeTypePrimitive('string'), new SerdeTypeNull());
        $this->partialSerdeTypeLookup->addSerdeTypeIfNeeded($typeString, $arrayType, $isRootType);

        // Resolve the real type of the array keys/values
        // When array of a type is a root type, the key/value types are still considered a root type
        $valueSerdeType = $this->resolveTypeToSerdeType($type->getValueType(), $isRootType);
        $keySerdeType = $this->resolveTypeToSerdeType($type->getKeyType(), $isRootType);

        if (!($keySerdeType instanceof SerdeTypePrimitive)) {
            throw new SerializationException(
                'Expected associative array to have primitive key type, but found' .
                $keySerdeType->displayName()
            );
        }

        // Update the placeholder
        SerdeTypeAssocArray::setKeyValueTypes($arrayType, $keySerdeType, $valueSerdeType);
    }

    /**
     * @throws ReflectionException|SerializationException
     */
    private function createSerdeTypeUnionIfNeeded(Compound $type, bool $isRootType): SerdeType
    {
        $typeString = $type->__toString();
        if (!$this->partialSerdeTypeLookup->contains($typeString)) {
            // Create placeholder union type and add it to the lookup, to break cycles
            $unionType = new SerdeTypeUnion([], null);
            $this->partialSerdeTypeLookup->addSerdeTypeIfNeeded($typeString, $unionType, $isRootType);

            $primitives = [];
            $nonPrimitive = null;
            foreach ($type->getIterator() as $subtype) {
                // Subtypes in a union are considered root types if the union is a root type
                $serdeSubtype = $this->resolveTypeToSerdeType($subtype, $isRootType);
                if ($serdeSubtype instanceof SerdeTypePrimitive) {
                    $primitives[] = $serdeSubtype;
                } elseif (($serdeSubtype instanceof SerdeTypeClass) || ($serdeSubtype instanceof SerdeTypeArray)) {
                    if ($nonPrimitive !== null) {
                        throw new SerializationException("Serde of union types with more than one non-primitive type are not supported: $type");
                    } else {
                        $nonPrimitive = $serdeSubtype;
                    }
                } else {
                    throw new SerializationException("Serde of union types only supported for primitives, classes, and arrays: $type");
                }
            }

            // Overwrite the values in the placeholder union type
            SerdeTypeUnion::setSubtypes($unionType, $primitives, $nonPrimitive);
        }
        return $this->partialSerdeTypeLookup->getSerdeType($typeString);
    }

    /**
     * @throws ReflectionException|SerializationException
     */
    private function createNullableSerdeTypeIfNeeded(Nullable $type, bool $isRootType): SerdeType
    {
        $typeString = $type->__toString();
        if (!$this->partialSerdeTypeLookup->contains($typeString)) {
            // Create placeholder union type and add it to the lookup, to break cycles
            $unionType = new SerdeTypeUnion([], null);
            $this->partialSerdeTypeLookup->addSerdeTypeIfNeeded($typeString, $unionType, $isRootType);

            // The inner type of nullable types are root types if the nullable type is a root type
            $innerType = $this->resolveTypeToSerdeType($type->getActualType(), $isRootType);
            if ($innerType instanceof SerdeTypePrimitive) {
                $primitives = [$innerType, new SerdeTypeNull()];
                $classOrArray = null;
            } elseif ($innerType instanceof SerdeTypeClass || $innerType instanceof SerdeTypeArray) {
                $primitives = [new SerdeTypeNull()];
                $classOrArray = $innerType;
            } else {
                throw new SerializationException('Unexpected nullable type: ' . $innerType->displayName());
            }

            // Overwrite the values in the placeholder union type
            SerdeTypeUnion::setSubtypes($unionType, $primitives, $classOrArray);
        }
        return $this->partialSerdeTypeLookup->getSerdeType($typeString);
    }

    /**
     * @throws ReflectionException|SerializationException
     */
    private function createSerdeTypeClassIfNeeded(Object_ $type, bool $isRootType): SerdeType
    {
        $fqsen = $type->getFqsen();
        if ($fqsen === null) {
            throw new SerializationException('Unsupported type "object"');
        }

        if (!$this->partialSerdeTypeLookup->contains($fqsen)) {
            // Add a SerdeTypeClass with a placeholder class definition, to break cycles
            $placeholderClassDef = new SerdeClassDefinition(null, [], []);
            $serdeType = new SerdeTypeClass($fqsen, $placeholderClassDef);
            $this->partialSerdeTypeLookup->addSerdeTypeIfNeeded($fqsen, $serdeType, $isRootType);

            // Create the actual class definition
            $classDefFactory = new SerdeClassDefinitionFactory($this);
            $classDefinition = $classDefFactory->createClassDefinition($fqsen, $isRootType);

            // Overwrite the placeholder class definition on the existing SerdeTypeClass reference
            SerdeTypeClass::setClassDefinition($serdeType, $classDefinition);
        }
        return $this->partialSerdeTypeLookup->getSerdeType($fqsen);
    }
}