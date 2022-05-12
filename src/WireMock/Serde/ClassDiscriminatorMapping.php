<?php

namespace WireMock\Serde;

class ClassDiscriminatorMapping implements ClassDiscriminator
{
    /** @var string */
    private $discriminatorPropName;
    /** @var string[] keyed by values of discriminator prop */
    private $discriminatorValueToClassMap;

    /**
     * @param string $discriminatorPropName
     * @param string[] $discriminatorValueToClassMap
     */
    public function __construct(string $discriminatorPropName, array $discriminatorValueToClassMap)
    {
        $this->discriminatorPropName = $discriminatorPropName;
        $this->discriminatorValueToClassMap = $discriminatorValueToClassMap;
    }

    function getDiscriminatedType($data): string
    {
        $discriminatorName = $this->discriminatorPropName;
        if (array_key_exists($discriminatorName, $data)) {
            $discriminatorValue = $data[$discriminatorName];
            $map = $this->discriminatorValueToClassMap;
            if (array_key_exists($discriminatorValue, $map)) {
                return $map[$discriminatorValue];
            }
        }
        throw new SerializationException("Could not discriminate class type because $discriminatorName not present in data");
    }
}