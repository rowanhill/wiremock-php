<?php

namespace WireMock\Client;

class XPathValueMatchingStrategy extends ValueMatchingStrategy
{
    /**
     * @var string|AdvancedPathPattern
     * @serde-named-by matchingType
     * @serde-possible-names matchingValueNames
     */
    protected $matchingValue;
    /** @var array|null */
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
        $this->xPathNamespaces[$name] = $namespaceUri;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getXPathNamespaces(): ?array
    {
        return $this->xPathNamespaces;
    }
}