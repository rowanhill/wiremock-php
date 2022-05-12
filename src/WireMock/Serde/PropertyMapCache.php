<?php

namespace WireMock\Serde;

use ReflectionClass;
use ReflectionException;
use WireMock\Serde\Type\SerdeType;
use WireMock\Serde\Type\SerdeTypeFactory;

class PropertyMapCache
{
    /** @var SerdeTypeFactory */
    private $serdeTypeFactory;
    /** @var PropertyMap[] keyed by FQN */
    private $cache;
    /** @var string[] */
    private $fqns;

    /**
     * @param ...$types string FQN of type
     * @throws ReflectionException|SerializationException
     */
    public function __construct(...$types)
    {
        $this->serdeTypeFactory = new SerdeTypeFactory();
        $this->fqns = array_map(function($fqn) { return $this->prependBackslashIfNeeded($fqn); }, $types);
        $this->cache = [];
        foreach ($this->fqns as $type) {
            $map = $this->propertyMapOf(new ReflectionClass($type));
            $this->cache[$type] = $map;
        }
    }

    /**
     * @throws SerializationException
     */
    public function getPropertyMap(string $t): PropertyMap
    {
        $type = $this->prependBackslashIfNeeded($t);
        if (!array_key_exists($type, $this->cache)) {
            throw new SerializationException("Type $type does not exist in the property map cache");
        }
        return $this->cache[$type];
    }

    /**
     * @throws SerializationException
     */
    public function getFullyQualifiedName(string $partialType): ?string
    {
        $partialType = $this->prependBackslashIfNeeded($partialType);
        $matches = array_filter(
            $this->fqns,
            function($fqn) use ($partialType) {
                return substr($fqn, -strlen($partialType)) === $partialType;
            }
        );
        if (count($matches) > 1) {
            throw new SerializationException("Found multiple matching FQNs ending $partialType");
        } else if (count($matches) === 1) {
            return array_pop($matches);
        } else {
            return null;
        }
    }

    /**
     * @throws SerializationException
     */
    private function propertyMapOf(ReflectionClass $refClass): PropertyMap
    {
        $properties = $this->getProperties($refClass);
        $mandatoryConstructorParams = $this->getMandatoryConstructorParams($refClass, $properties);

        foreach ($mandatoryConstructorParams as $param) {
            if (array_key_exists($param->name, $properties)) {
                unset($properties[$param->name]);
            }
        }

        return new PropertyMap($mandatoryConstructorParams, $properties);
    }

    /**
     * @param ReflectionClass $refClass
     * @return SerdeProp[] keyed by prop name
     * @throws SerializationException
     */
    private function getProperties(ReflectionClass $refClass): array
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
            $propType = $this->getTypeFromPropertyComment($docComment);
            $serdeProp = new SerdeProp($propName, $propType);
            $result[$propName] = $serdeProp;
        }
        return $result;
    }

    /**
     * @throws SerializationException
     */
    private function getTypeFromPropertyComment(string $docComment): SerdeType
    {
        $matches = array();
        if (preg_match('/@var\s+(\\\?array<[^>]+>|\S+)/', $docComment, $matches) !== 1) {
            throw new SerializationException("No @var annotation in comment:\n$docComment");
        }
        return $this->serdeTypeFactory->parseTypeString($matches[1], $this);
    }

    /**
     * @param ReflectionClass $refClass
     * @param SerdeProp[] $properties keyed by prop name
     * @return SerdeProp[] in constructor args order
     * @throws SerializationException
     */
    private function getMandatoryConstructorParams(ReflectionClass $refClass, array $properties): array
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
        $paramTypes = [];
        foreach ($refParams as $refParam) {
            if ($refParam->isDefaultValueAvailable()) {
                break;
            }
            $paramName = $refParam->getName();
            if (array_key_exists($paramName, $properties)) {
                $result[] = $properties[$paramName];
                $paramTypes[] = $paramName;
            } else {
                if ($docComment === false) {
                    throw new SerializationException(
                        "Could not find type for constructor param $paramName on type $refClass->name because it has no type"
                    );
                }
                $paramType = $this->getTypeFromParamComment($paramName, $docComment);
                $serdeProp = new SerdeProp($paramName, $paramType);
                $result[] = $serdeProp;
                $paramTypes[] = $paramType->displayName();
            }
        }
        return $result;
    }

    /**
     * @throws SerializationException
     */
    private function getTypeFromParamComment(string $paramName, string $docComment): SerdeType
    {
        $matches = array();
        if (preg_match("/@param\\s+(?:\\$$paramName\\s+(\\S+))|(?:(\\S+)\\s+\\$$paramName)/", $docComment, $matches) !== 1) {
            throw new SerializationException("No @param annotation for $paramName in comment:\n$docComment");
        }
        $type = empty($matches[1]) ? $matches[2] : $matches[1];
        return $this->serdeTypeFactory->parseTypeString($type, $this);
    }

    private function prependBackslashIfNeeded(string $str): string
    {
        if (substr($str, 0, 1) === '\\') {
            return $str;
        } else {
            return '\\'.$str;
        }
    }
}