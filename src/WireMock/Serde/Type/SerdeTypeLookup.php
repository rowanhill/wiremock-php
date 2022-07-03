<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\CanonicalNameUtils;
use WireMock\Serde\SerializationException;

class SerdeTypeLookup
{
    /** @var array<string, SerdeType> */
    protected $lookup;
    /** @var array<string, bool> */
    protected $rootTypes;

    /**
     * @param array<string, SerdeType> $lookup
     * @param array<string, bool> $rootTypes
     */
    public function __construct(array $lookup, array $rootTypes)
    {
        $this->lookup = $lookup;
        $this->rootTypes = $rootTypes;
    }

    /**
     * @throws SerializationException
     */
    public function getSerdeType(string $type): SerdeType
    {
        $key = CanonicalNameUtils::stripLeadingBackslashIfNeeded($type);
        if (!array_key_exists($key, $this->lookup)) {
            throw new SerializationException("Type $key does not exist in the serde type cache");
        }
        return $this->lookup[$key];
    }

    public function getSerdeTypeIfExits(string $type): ?SerdeType
    {
        $key = CanonicalNameUtils::stripLeadingBackslashIfNeeded($type);
        return array_key_exists($key, $this->lookup) ?
            $this->lookup[$key] :
            null;
    }

    public function isRootType(string $type): bool
    {
        $key = CanonicalNameUtils::stripLeadingBackslashIfNeeded($type);
        return array_key_exists($key, $this->rootTypes);
    }
}