<?php

namespace WireMock\SerdeGen;

use WireMock\Serde\SerializationException;

class CanonicalNameFormer
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

    public static function prependBackslashIfNeeded(string $str): string
    {
        if (substr($str, 0, 1) === '\\') {
            return $str;
        } else {
            return '\\'.$str;
        }
    }

    /**
     * @throws SerializationException
     */
    public function getFullyQualifiedName(string $partialType): ?string
    {
        $partialType = self::prependBackslashIfNeeded($partialType);
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