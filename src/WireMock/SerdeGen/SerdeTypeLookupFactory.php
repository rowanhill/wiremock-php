<?php

namespace WireMock\SerdeGen;

use ReflectionException;
use WireMock\Serde\CanonicalNameUtils;
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
            return CanonicalNameUtils::stripLeadingBackslashIfNeeded($fqn);
        }, $types);
        $partialLookup->addRootTypes(...$canonicalTypes);
        $serdeTypeParser = new SerdeTypeParser($partialLookup);
        foreach ($canonicalTypes as $type) {
            // This creates the SerdeType and adds it to the lookup as a side-effect
            $serdeTypeParser->parseTypeString($type);
        }
        return $partialLookup;
    }
}