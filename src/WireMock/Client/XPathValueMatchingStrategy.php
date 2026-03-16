<?php

namespace WireMock\Client;

class XPathValueMatchingStrategy extends ValueMatchingStrategy
{
    /** @var array<string, string>|null */
    private $xPathNamespaces;

    /**
     * @param string|AdvancedPathPattern $matchingValue
     */
    public function __construct($matchingValue)
    {
        parent::__construct('matchesXPath', $matchingValue);
    }

    /**
     * @param string $name
     * @param string $namespaceUri
     * @return XPathValueMatchingStrategy
     */
    public function withXPathNamespace($name, $namespaceUri)
    {
        if (!isset($this->xPathNamespaces)) {
            $this->xPathNamespaces = [];
        }

        $this->xPathNamespaces[$name] = $namespaceUri;
        return $this;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getXPathNamespaces(): ?array
    {
        return $this->xPathNamespaces;
    }
}