<?php

namespace WireMock\Client;

use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;

class XPathValueMatchingStrategy extends ValueMatchingStrategy implements PostNormalizationAmenderInterface, PreDenormalizationAmenderInterface
{
    /** @var array */
    private $xPathNamespaces = array();
    /** @var ValueMatchingStrategy */
    private $valueMatchingStrategy;

    /**
     * XPathValueMatchingStrategy constructor.
     * @param string $matchingValue
     * @param ValueMatchingStrategy $valueMatchingStrategy
     */
    public function __construct($matchingValue, $valueMatchingStrategy = null)
    {
        parent::__construct('matchesXPath', $matchingValue);
        $this->valueMatchingStrategy = $valueMatchingStrategy;
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

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        $normalisedArray = parent::amendPostNormalisation($normalisedArray, $object);
        if (isset($normalisedArray['valueMatchingStrategy'])) {
            $strategy = $normalisedArray['valueMatchingStrategy'];
            unset($normalisedArray['valueMatchingStrategy']);
            $xPathExpression = $normalisedArray['matchesXPath'];
            $strategy['expression'] = $xPathExpression;
            $normalisedArray['matchesXPath'] = $strategy;
        }
        return $normalisedArray;
    }

    public static function amendPreDenormalisation(array $normalisedArray): array
    {
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingValue']);
        if (is_array($matchingValue)) {
            $xPath = $matchingValue['expression'];
            unset($matchingValue['expression']);
            $normalisedArray['valueMatchingStrategy'] = $matchingValue;
        } else {
            $xPath = $matchingValue;
        }
        $normalisedArray['matchingValue'] = $xPath;
        return $normalisedArray;
    }
}