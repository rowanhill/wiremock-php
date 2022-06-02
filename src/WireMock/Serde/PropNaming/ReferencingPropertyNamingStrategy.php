<?php

namespace WireMock\Serde\PropNaming;

use WireMock\Serde\SerializationException;

class ReferencingPropertyNamingStrategy implements PropertyNamingStrategy
{
    /** @var string */
    public $namingPropertyName;
    /** @var string */
    public $possibleNamesGenerator;

    public function __construct(string $namingPropertyName, string $possibleNamesGenerator)
    {
        $this->namingPropertyName = $namingPropertyName;
        $this->possibleNamesGenerator = $possibleNamesGenerator;
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
}