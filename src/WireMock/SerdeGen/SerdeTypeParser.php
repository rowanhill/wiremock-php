<?php

namespace WireMock\SerdeGen;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
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
use WireMock\Serde\SerdeClassDefinition;
use WireMock\Serde\PropNaming\ConstantPropertyNamingStrategy;
use WireMock\Serde\PropNaming\ReferencingPropertyNamingStrategy;
use WireMock\Serde\SerdeClassDiscriminationInfo;
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
use WireMock\SerdeGen\Tag\SerdeCatchAllTag;
use WireMock\SerdeGen\Tag\SerdeDiscriminateTypeTag;
use WireMock\SerdeGen\Tag\SerdeNamedByTag;
use WireMock\SerdeGen\Tag\SerdeNameTag;
use WireMock\SerdeGen\Tag\SerdePossibleNamesTag;
use WireMock\SerdeGen\Tag\SerdePossibleSubtypeTag;
use WireMock\SerdeGen\Tag\SerdeUnwrappedTag;

class SerdeTypeParser
{
    /** @var PartialSerdeTypeLookup */
    private $partialSerdeTypeLookup;
    /** @var DocBlockFactory */
    private $docBlockFactory;

    /**
     * @param PartialSerdeTypeLookup $partialSerdeTypeLookup
     */
    public function __construct(PartialSerdeTypeLookup $partialSerdeTypeLookup)
    {
        $this->partialSerdeTypeLookup = $partialSerdeTypeLookup;
        $this->docBlockFactory = DocBlockFactory::createInstance([
            'serde-name' => SerdeNameTag::class,
            'serde-unwrapped' => SerdeUnwrappedTag::class,
            'serde-named-by' => SerdeNamedByTag::class,
            'serde-possible-names' => SerdePossibleNamesTag::class,
            'serde-discriminate-type' => SerdeDiscriminateTypeTag::class,
            'serde-possible-subtype' => SerdePossibleSubtypeTag::class,
        ]);
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

            if (!$this->partialSerdeTypeLookup->contains($fqsen)) {
                // Add a SerdeTypeClass with a placeholder class definition, to break cycles
                $placeholderClassDef = new SerdeClassDefinition(null, [], []);
                $serdeType = new SerdeTypeClass($fqsen, $placeholderClassDef);
                $this->partialSerdeTypeLookup->addSerdeType($fqsen, $serdeType);

                // Create the actual class definition
                $classDefinition = $this->createClassDefinition($fqsen);

                // Overwrite the placeholder class definition on the existing SerdeTypeClass reference
                // Because this property is private, we do so via reflection
                $refProp = new ReflectionProperty($serdeType, 'classDefinition');
                $refProp->setAccessible(true);
                $refProp->setValue($serdeType, $classDefinition);
            }
            return $this->partialSerdeTypeLookup->getSerdeType($fqsen);
        } elseif ($type instanceof String_) {
            return new SerdeTypePrimitive('string');
        } else {
            throw new SerializationException('Unexpected type ' . get_class($type) . ": $type");
        }
    }

    /**
     * @throws SerializationException|ReflectionException
     */
    private function createClassDefinition(string $classType): SerdeClassDefinition
    {
        $refClass = new ReflectionClass($classType);
        $contextFactory = new ContextFactory();
        $context = $contextFactory->createFromReflector($refClass);

        $classDiscriminationInfo = $this->getClassDiscriminationInfo($refClass, $context);
        $allProperties = $this->getProperties($refClass, $context);
        $mandatoryConstructorParams = $this->getMandatoryConstructorParams($refClass, $allProperties, $context);

        return new SerdeClassDefinition($classDiscriminationInfo, $mandatoryConstructorParams, $allProperties);
    }

    /**
     * @throws SerializationException
     * @throws ReflectionException
     */
    private function getClassDiscriminationInfo(ReflectionClass $refClass, Context $context): ?SerdeClassDiscriminationInfo
    {
        if ($refClass->getDocComment() === false) {
            return null;
        }

        $docBlock = $this->docBlockFactory->create($refClass, $context);

        /** @var SerdeDiscriminateTypeTag|null $serdeNameTag */
        $serdeNameTag = $this->getSingleClassTagIfPresent($docBlock, 'serde-discriminate-type', $refClass->getShortName());

        if ($serdeNameTag === null) {
            return null;
        }

        /** @var SerdePossibleSubtypeTag $subtypeTags */
        $subtypeTags = $docBlock->getTagsByName('serde-possible-subtype');
        foreach ($subtypeTags as $subtypeTag) {
            // Resolve the subtype, just to make sure it's in the lookup
            $this->resolveTypeToSerdeType($subtypeTag->getType());
        }

        return new SerdeClassDiscriminationInfo(
            $refClass->getName() . '::' . $serdeNameTag->getDiscriminatorFactory()
        );
    }

    /**
     * @param ReflectionClass $refClass
     * @param Context $context
     * @return SerdeProp[]
     * @throws ReflectionException
     * @throws SerializationException
     */
    private function getProperties(ReflectionClass $refClass, Context $context): array
    {
        $refProps = [];
        do {
            // Merge the props, ignoring antecedent props that have already been defined on descendants
            foreach ($refClass->getProperties() as $refProp) {
                if (array_key_exists($refProp->name, $refProps)) {
                    continue;
                }
                $refProps[$refProp->name] = $refProp;
            }
            $refClass = $refClass->getParentClass();
        } while ($refClass !== false);
        $result = array();
        foreach ($refProps as $refProp) {
            if ($refProp->isStatic()) {
                continue;
            }
            $propName = $refProp->getName();
            $docBlock = $this->docBlockFactory->create($refProp, $context);

            $varTags = $docBlock->getTagsByName('var');
            if (count($varTags) !== 1) {
                throw new SerializationException("Expected exactly 1 @var tag on property $propName but found "
                    . count($varTags));
            }
            /** @var Var_ $varTag */
            $varTag = $varTags[0];
            $propType = $this->resolveTypeToSerdeType($varTag->getType());

            /** @var SerdeNameTag|null $serdeNameTag */
            $serdeNameTag = $this->getSinglePropTagIfPresent($docBlock, 'serde-name', $propName);

            /** @var SerdeNamedByTag|null $serdeNamedByTag */
            $serdeNamedByTag = $this->getSinglePropTagIfPresent($docBlock, 'serde-named-by', $propName);

            /** @var SerdePossibleNamesTag|null $serdePossibleNamesTag */
            $serdePossibleNamesTag = $this->getSinglePropTagIfPresent($docBlock, 'serde-possible-names', $propName);

            $namingStrategy = null;
            if ($serdeNameTag && $serdeNamedByTag) {
                throw new SerializationException("Property $propName on $refClass has both @serde-name and @serde-named-by tags, but only (max) one is allowed");
            } else if ($serdeNameTag) {
                $serializedPropName = $serdeNameTag->getSerializedPropertyName();
                $namingStrategy = new ConstantPropertyNamingStrategy($serializedPropName);
            } elseif ($serdeNamedByTag) {
                if ($serdePossibleNamesTag === null) {
                    throw new SerializationException("Property $propName has @serde-named-by, so must also have @serde-possible-names");
                }
                $namingPropName = $serdeNamedByTag->getNamingPropertyName();
                $possibleNamesGenerator = $serdePossibleNamesTag->getPossibleNamesGenerator();
                $fqMethodName = $refProp->class.'::'.$possibleNamesGenerator;
                $namingStrategy = new ReferencingPropertyNamingStrategy($namingPropName, $fqMethodName);
            }

            /** @var SerdeUnwrappedTag|null $serdeUnwrappedTag */
            $serdeUnwrappedTag = $this->getSinglePropTagIfPresent($docBlock, 'serde-unwrapped', $propName);
            $unwrapped = !!$serdeUnwrappedTag;

            /** @var SerdeCatchAllTag|null $serdeCatchAllTag */
            $serdeCatchAllTag = $this->getSinglePropTagIfPresent($docBlock, 'serde-catch-all', $propName);
            $catchAll = !!$serdeCatchAllTag;

            if ($unwrapped && $catchAll) {
                throw new SerializationException("Property $propName on $refClass has both @serde-unwrapped and @serde-catch-all, but only (max) one is allowed");
            }
            if ($catchAll && !($propType instanceof SerdeTypeArray || ($propType instanceof SerdeTypeUnion && $propType->isNullableArray()))) {
                throw new SerializationException("Catch-all property $propName on $refClass must be some kind of array type, but is " . $propType->displayName());
            }

            $serdeProp = new SerdeProp($propName, $refProp->class, $propType, $namingStrategy, $unwrapped, $catchAll);
            $result[] = $serdeProp;
        }
        return $result;
    }

    /**
     * @throws SerializationException
     */
    private function getSingleClassTagIfPresent(DocBlock $docBlock, string $tagName, string $className): ?DocBlock\Tag
    {
        return $this->getSingleTagIfPresent($docBlock, $tagName, "class $className");
    }

    /**
     * @throws SerializationException
     */
    private function getSinglePropTagIfPresent(DocBlock $docBlock, string $tagName, string $propName): ?DocBlock\Tag
    {
        return $this->getSingleTagIfPresent($docBlock, $tagName, "property $propName");
    }

    /**
     * @throws SerializationException
     */
    private function getSingleTagIfPresent(DocBlock $docBlock, string $tagName, string $entity): ?DocBlock\Tag
    {
        $tags = $docBlock->getTagsByName($tagName);
        if (count($tags) > 1) {
            throw new SerializationException("Expected 0 or 1 @$tagName tag on $entity but "
                . ' found ' . count($tags));
        }
        if (count($tags) === 1) {
            return $tags[0];
        } else {
            return null;
        }
    }

    /**
     * Gets all mandatory constructor arg SerdeProps, in order, as references to elements in $properties. Adds new
     * elements to $properties if necessary.
     *
     * @param ReflectionClass $refClass
     * @param SerdeProp[] $properties
     * @param Context $context
     * @return SerdeProp[] in constructor args order
     * @throws ReflectionException
     * @throws SerializationException
     */
    private function getMandatoryConstructorParams(ReflectionClass $refClass, array &$properties, Context $context): array
    {
        $constructor = $refClass->getConstructor();
        if ($constructor === null) {
            return array();
        }

        $refParams = $constructor->getParameters();
        if (empty($refParams)) {
            return array();
        }

        if ($constructor->getDocComment()) {
            $docBlock = $this->docBlockFactory->create($constructor, $context);
            /** @var Param[] $paramTags */
            $paramTags = $docBlock->getTagsByName('param');
        } else {
            $paramTags = [];
        }

        /** @var SerdeProp[] $result */
        $result = array();
        foreach ($refParams as $refParam) {
            if ($refParam->isDefaultValueAvailable()) {
                break;
            }
            $paramName = $refParam->getName();

            $paramExistsAsVarProp = false;
            foreach ($properties as &$prop) {
                if ($prop->name === $paramName) {
                    $result[] = &$prop;
                    $paramExistsAsVarProp = true;
                    break;
                }
            }
            if (!$paramExistsAsVarProp) {
                $found = false;
                foreach ($paramTags as $paramTag) {
                    if ($paramTag->getVariableName() === $paramName) {
                        $paramType = $this->resolveTypeToSerdeType($paramTag->getType());
                        $serdeProp = new SerdeProp($paramName, $refParam->getDeclaringClass()->name, $paramType);
                        // This is a constructor-only prop, so shouldn't be included in the serialized (or therefore
                        // denormalized) form
                        $serdeProp->includeInNormalizedForm = false;
                        $properties[] = $serdeProp;
                        $result[] = &$properties[count($properties) - 1];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    throw new SerializationException("Could not find type for constructor param $paramName on type $refClass->name because it has no @param tag in the constructor docblock");
                }
            }
        }
        return $result;
    }
}