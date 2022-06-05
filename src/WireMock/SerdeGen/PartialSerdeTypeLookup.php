<?php

namespace WireMock\SerdeGen;


use WireMock\Serde\CanonicalNameUtils;
use WireMock\Serde\SerializationException;
use WireMock\Serde\Type\SerdeType;
use WireMock\Serde\Type\SerdeTypeLookup;

class PartialSerdeTypeLookup extends SerdeTypeLookup
{
    public function __construct()
    {
        parent::__construct([], []);
    }

    public function addRootTypes(...$types)
    {
        foreach ($types as $type) {
            $key = CanonicalNameUtils::stripLeadingBackslashIfNeeded($type);
            $this->rootTypes[$key] = true;
        }
    }

    /**
     * @throws SerializationException
     */
    public function addSerdeTypeIfNeeded(string $type, SerdeType $serdeType, bool $isRootType): SerdeType
    {
        if ($isRootType) {
            $this->addRootTypes($type);
        }
        if (!$this->contains($type)) {
            $this->addSerdeType($type, $serdeType);
        }
        return $this->getSerdeType($type);
    }

    private function addSerdeType(string $type, SerdeType $serdeType)
    {
        $key = CanonicalNameUtils::stripLeadingBackslashIfNeeded($type);
        $this->lookup[$key] = $serdeType;
    }

    public function contains(string $type): bool
    {
        $key = CanonicalNameUtils::stripLeadingBackslashIfNeeded($type);
        return isset($this->lookup[$key]);
    }
}