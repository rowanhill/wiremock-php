<?php

namespace WireMock\Client;

class XPathValueMatchingStrategy extends ValueMatchingStrategy
{
    /** @var array */
    private $_namespaces = array();

    /**
     * @param string $name
     * @param string $namespaceUri
     * @return XPathValueMatchingStrategy
     */
    public function withXPathNamespace($name, $namespaceUri)
    {
        $this->_namespaces[$name] = $namespaceUri;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        if (!empty($this->_namespaces)) {
            $array['xPathNamespaces'] = $this->_namespaces;
        }
        return $array;
    }
}