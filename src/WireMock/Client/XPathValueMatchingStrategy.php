<?php

namespace WireMock\Client;

class XPathValueMatchingStrategy extends ValueMatchingStrategy
{
    /** @var array */
    private $_namespaces = array();
    /** @var ValueMatchingStrategy */
    private $_valueMatchingStrategy;

    /**
     * XPathValueMatchingStrategy constructor.
     * @param string $matchingValue
     * @param ValueMatchingStrategy $valueMatchingStrategy
     */
    public function __construct($matchingValue, $valueMatchingStrategy = null)
    {
        parent::__construct('matchesXPath', $matchingValue);
        $this->_valueMatchingStrategy = $valueMatchingStrategy;
    }

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
        if (!$this->_valueMatchingStrategy) {
            $array = parent::toArray();
            if (!empty($this->_namespaces)) {
                $array['xPathNamespaces'] = $this->_namespaces;
            }
            return $array;
        } else {
            return array(
                'matchesXPath' => array_merge(
                    array(
                        'expression' => $this->_matchingValue
                    ),
                    $this->_valueMatchingStrategy->toArray()
                )
            );
        }
    }
}