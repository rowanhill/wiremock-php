<?php

namespace WireMock\SerdeGen;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use WireMock\Serde\PropertyMap;
use WireMock\Serde\SerdeProp;
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
     * @param $context Context|null
     * @throws SerializationException|ReflectionException
     */
    public function parseTypeString(string $type, Context $context = null): SerdeType
    {
        $typeResolver = new TypeResolver();
        $resolvedType = $typeResolver->resolve($type, $context);

        return $this->resolveTypeToSerdeType($resolvedType);
    }

    /**
     * Translate from a phpDocumentor Type to a wiremock-php SerdeType
     * @throws SerializationException
     * @throws ReflectionException
     */
    private function resolveTypeToSerdeType(Type $type): SerdeType
    {
        if ($type instanceof Array_) {
            $typeString = $type->__toString();
            if ($typeString === 'array') {
                return new SerdeTypeUntypedArray();
            }
            $valueSerdeType = $this->resolveTypeToSerdeType($type->getValueType());
            if (substr($typeString, -2) === '[]') {
                return new SerdeTypeTypedArray($valueSerdeType);
            }
            $keySerdeType = $this->resolveTypeToSerdeType($type->getKeyType());
            if (!($keySerdeType instanceof SerdeTypePrimitive)) {
                throw new SerializationException(
                    'Expected associative array to have primitive key type, but found' .
                        $keySerdeType->displayName()
                );
            }
            return new SerdeTypeAssocArray($keySerdeType, $valueSerdeType);
        } elseif ($type instanceof Boolean) {
            return new SerdeTypePrimitive('bool');
        } elseif ($type instanceof Compound) {
            $primitives = [];
            $nonPrimitive = null;
            foreach ($type->getIterator() as $subtype) {
                $serdeSubtype = $this->resolveTypeToSerdeType($subtype);
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
            return new SerdeTypeUnion($primitives, $nonPrimitive);
        } elseif ($type instanceof Float_) {
            return new SerdeTypePrimitive('float');
        } elseif ($type instanceof Integer) {
            return new SerdeTypePrimitive('int');
        } elseif ($type instanceof Null_) {
            return new SerdeTypeNull();
        } elseif ($type instanceof Nullable) {
            $innerType = $this->resolveTypeToSerdeType($type->getActualType());
            if ($innerType instanceof SerdeTypePrimitive) {
                return new SerdeTypeUnion([$innerType, new SerdeTypeNull()], null);
            } elseif ($innerType instanceof SerdeTypeClass || $innerType instanceof SerdeTypeArray) {
                return new SerdeTypeUnion([new SerdeTypeNull()], $innerType);
            } else {
                throw new SerializationException('Unexpected nullable type: ' . $innerType->displayName());
            }
        } elseif ($type instanceof Object_) {
            $fqsen = $type->getFqsen();
            if ($fqsen === null) {
                throw new SerializationException('Unsupported use of type "object"');
            }

            if (!$this->partialSerdeTypeLookup->contains($fqsen, false)) {
                // Add a SerdeTypeClass with a placeholder property map, to break cycles
                $placeholderPropMap = new PropertyMap([], []);
                $serdeType = new SerdeTypeClass($fqsen, $placeholderPropMap);
                $this->partialSerdeTypeLookup->addSerdeType($fqsen, false, $serdeType);

                // Create the actual property map
                $propertyMap = $this->createPropertyMap($fqsen);

                // Overwrite the placeholder property map on the existing SerdeTypeClass reference
                // Because this property is private, we do so via reflection
                $refProp = new ReflectionProperty($serdeType, 'propertyMap');
                $refProp->setAccessible(true);
                $refProp->setValue($serdeType, $propertyMap);
            }
            return $this->partialSerdeTypeLookup->getSerdeType($fqsen, false);
        } elseif ($type instanceof String_) {
            return new SerdeTypePrimitive('string');
        } else {
            throw new SerializationException('Unexpected type ' . get_class($type) . ": $type");
        }
    }

    /**
     * @throws SerializationException|ReflectionException
     */
    private function createPropertyMap(string $classType): PropertyMap
    {
        $refClass = new ReflectionClass($classType);
        $contextFactory = new ContextFactory();
        $context = $contextFactory->createFromReflector($refClass);

        $properties = $this->getProperties($refClass, $context);
        $mandatoryConstructorParams = $this->getMandatoryConstructorParams($refClass, $properties, $context);

        foreach ($mandatoryConstructorParams as $param) {
            if (array_key_exists($param->name, $properties)) {
                unset($properties[$param->name]);
            }
        }

        return new PropertyMap($mandatoryConstructorParams, $properties);
    }

    /**
     * @param ReflectionClass $refClass
     * @param Context $context
     * @return SerdeProp[] keyed by prop name
     * @throws ReflectionException
     * @throws SerializationException
     */
    private function getProperties(ReflectionClass $refClass, Context $context): array
    {
        $refProps = $refClass->getProperties();
        $result = array();
        foreach ($refProps as $refProp) {
            if ($refProp->isStatic()) {
                continue;
            }
            $propName = $refProp->getName();
            $docComment = $refProp->getDocComment();
            if ($docComment === false) {
                throw new SerializationException("The property $propName on class $refClass->name has no doc comment");
            }
            $propType = $this->getTypeFromPropertyComment($docComment, $context);
            $serdeProp = new SerdeProp($propName, $propType);
            $result[$propName] = $serdeProp;
        }
        return $result;
    }

    /**
     * @throws SerializationException
     * @throws ReflectionException
     */
    private function getTypeFromPropertyComment(string $docComment, Context $context): SerdeType
    {
        $matches = array();
        if (preg_match('/@var\s+(\\\?array<[^>]+>|\S+)/', $docComment, $matches) !== 1) {
            throw new SerializationException("No @var annotation in comment:\n$docComment");
        }
        return $this->parseTypeString($matches[1], $context);
    }

    /**
     * @param ReflectionClass $refClass
     * @param SerdeProp[] $properties keyed by prop name
     * @param Context $context
     * @return SerdeProp[] in constructor args order
     * @throws ReflectionException
     * @throws SerializationException
     */
    private function getMandatoryConstructorParams(ReflectionClass $refClass, array $properties, Context $context): array
    {
        $constructor = $refClass->getConstructor();
        if ($constructor === null) {
            return array();
        }

        $refParams = $constructor->getParameters();
        if (empty($refParams)) {
            return array();
        }

        $docComment = $constructor->getDocComment();

        /** @var SerdeProp[] $result */
        $result = array();
        foreach ($refParams as $refParam) {
            if ($refParam->isDefaultValueAvailable()) {
                break;
            }
            $paramName = $refParam->getName();
            if (array_key_exists($paramName, $properties)) {
                $result[] = $properties[$paramName];
            } else {
                if ($docComment === false) {
                    throw new SerializationException(
                        "Could not find type for constructor param $paramName on type $refClass->name because it has no type"
                    );
                }
                $paramType = $this->getTypeFromParamComment($paramName, $docComment, $context);
                $serdeProp = new SerdeProp($paramName, $paramType);
                $result[] = $serdeProp;
            }
        }
        return $result;
    }

    /**
     * @throws SerializationException
     * @throws ReflectionException
     */
    private function getTypeFromParamComment(string $paramName, string $docComment, Context $context): SerdeType
    {
        $matches = array();
        if (preg_match("/@param\\s+(?:\\$$paramName\\s+(\\S+))|(?:(\\S+)\\s+\\$$paramName)/", $docComment, $matches) !== 1) {
            throw new SerializationException("No @param annotation for $paramName in comment:\n$docComment");
        }
        $type = empty($matches[1]) ? $matches[2] : $matches[1];
        return $this->parseTypeString($type, $context);
    }
}