<?php

namespace WireMock\Serde\PropNaming;

use ReflectionException;
use ReflectionMethod;
use WireMock\Serde\SerializationException;

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

        // Validate the method is static and has no required params
        $refMethod = new ReflectionMethod($possibleNamesGenerator);
        if (!$refMethod->isStatic()) {
            throw new SerializationException("Methods used with @serde-possible-names must be static, but $possibleNamesGenerator is not");
        }
        $numRequiredParams = $refMethod->getNumberOfRequiredParameters();
        if ($numRequiredParams > 0) {
            throw new SerializationException("Methods used with @serde-possible-names must take no required args, but $possibleNamesGenerator requires $numRequiredParams");
        }
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
        $refMethod = new ReflectionMethod($this->possibleNamesGenerator);
        $refMethod->setAccessible(true);
        return $refMethod->invoke(null);
    }
}