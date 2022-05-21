<?php

namespace WireMock\SerdeGen;

use ReflectionException;
use WireMock\Serde\SerializationException;
use WireMock\Serde\Type\SerdeTypeLookup;

class SerdeTypeLookupFactory
{
    /**
     * @throws ReflectionException|SerializationException
     */
    public static function createLookup(...$types): SerdeTypeLookup
    {
        $partialLookup = new PartialSerdeTypeLookup();
        $canonicalTypes = array_map(function($fqn) {
            return CanonicalNameFormer::prependBackslashIfNeeded($fqn);
        }, $types);
        $canonicalNameFormer = new CanonicalNameFormer($canonicalTypes);
        $serdeTypeFactory = new SerdeTypeFactory($partialLookup, $canonicalNameFormer);
        foreach ($canonicalTypes as $type) {
            // This creates the SerdeType and adds it to the lookup as a side-effect
            $serdeTypeFactory->parseTypeString($type);
        }
        return $partialLookup;
    }

}