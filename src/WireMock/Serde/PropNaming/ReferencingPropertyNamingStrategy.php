<?php

namespace WireMock\Serde\PropNaming;

use ReflectionException;
use WireMock\Serde\MethodFactory;
use WireMock\Serde\SerializationException;
use WireMock\Serde\StaticFactoryMethodValidator;

class ReferencingPropertyNamingStrategy implements PropertyNamingStrategy
{
    /** @var string */
    public $namingPropertyName;
    /**
     * The fully qualified name of a static method (with no required params) that returns an array of strings, which
     * are the possible serialized names for the prop using this naming strategy
     * @var string
     */
    public $possibleNamesGenerator;

    /**
     * @throws ReflectionException
     * @throws SerializationException
     */
    public function __construct(string $namingPropertyName, string $possibleNamesGenerator)
    {
        $this->namingPropertyName = $namingPropertyName;
        $this->possibleNamesGenerator = $possibleNamesGenerator;
        StaticFactoryMethodValidator::validate($possibleNamesGenerator);
    }

    function getSerializedName(array $data): string
    {
        if (!array_key_exists($this->namingPropertyName, $data)) {
            throw new SerializationException("Cannot name property based on value of $this->namingPropertyName, because it is missing from data");
        }
        $value = $data[$this->namingPropertyName];
        if (!is_string($value)) {
            throw new SerializationException("Cannot name property based on value of $this->namingPropertyName, because that value is of type " . gettype($value));
        }
        return $value;
    }

    /**
     * @return string[]
     * @throws ReflectionException
     */
    function getPossibleSerializedNames(): array
    {
        $refMethod = MethodFactory::createMethod($this->possibleNamesGenerator);
        $refMethod->setAccessible(true);
        return $refMethod->invoke(null);
    }
}