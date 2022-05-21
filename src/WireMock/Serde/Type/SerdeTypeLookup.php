<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;
use WireMock\SerdeGen\CanonicalNameFormer;

class SerdeTypeLookup
{
    /** @var array<string, SerdeType> */
    protected $lookup;

    /**
     * @param array<string, SerdeType> $lookup
     */
    public function __construct(array $lookup)
    {
        $this->lookup = $lookup;
    }

    /**
     * @throws SerializationException
     */
    public function getSerdeType(string $type, bool $isNullable): SerdeType
    {
        $canonicalType = CanonicalNameFormer::prependBackslashIfNeeded($type);
        $key = $this->getKey($canonicalType, $isNullable);
        if (!array_key_exists($key, $this->lookup)) {
            throw new SerializationException("Type $key does not exist in the serde type cache");
        }
        return $this->lookup[$key];
    }

    protected function getKey(string $type, bool $isNullable): string
    {
        return $type . '__nullable_' . ($isNullable ? 'true' : 'false');
    }
}