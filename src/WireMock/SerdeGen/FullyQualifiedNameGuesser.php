<?php

namespace WireMock\SerdeGen;

use WireMock\Serde\CanonicalNameUtils;
use WireMock\Serde\SerializationException;

class FullyQualifiedNameGuesser
{
    /** @var string[] */
    private $canonicalNames;

    /**
     * @param string[] $canonicalNames
     */
    public function __construct(array $canonicalNames)
    {
        $this->canonicalNames = $canonicalNames;
    }

    /**
     * @throws SerializationException
     */
    public function getFullyQualifiedName(string $partialType): ?string
    {
        $partialType = CanonicalNameUtils::prependBackslashIfNeeded($partialType);
        $matches = array_filter(
            $this->canonicalNames,
            function($fqn) use ($partialType) {
                return substr($fqn, -strlen($partialType)) === $partialType;
            }
        );
        if (count($matches) > 1) {
            throw new SerializationException("Found multiple matching FQNs ending $partialType");
        } else if (count($matches) === 1) {
            return array_pop($matches);
        } else {
            return null;
        }
    }
}