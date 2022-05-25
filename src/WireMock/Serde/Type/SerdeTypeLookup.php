<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\CanonicalNameUtils;
use WireMock\Serde\SerializationException;

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
    public function getSerdeType(string $type): SerdeType
    {
        $key = CanonicalNameUtils::prependBackslashIfNeeded($type);
        if (!array_key_exists($key, $this->lookup)) {
            throw new SerializationException("Type $key does not exist in the serde type cache");
        }
        return $this->lookup[$key];
    }
}