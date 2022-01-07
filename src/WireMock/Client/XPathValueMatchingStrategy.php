<?php

namespace WireMock\Client;

use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PostNormalizationAmenderInterface;

class XPathValueMatchingStrategy extends ValueMatchingStrategy implements PostNormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    /** @var array */
    private $_xPathNamespaces = array();
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
        $this->_xPathNamespaces[$name] = $namespaceUri;
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

    public static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        unset($normalisedArray['matchingType']); // matchesXPath
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingValue']);
        if (is_array($matchingValue)) {
            $xPath = $matchingValue['expression'];
            unset($matchingValue['expression']);
            $normalisedArray['valueMatchingStrategy'] = $matchingValue;
        } else {
            $xPath = $matchingValue;
        }
        return new ObjectToPopulateResult(new self($xPath), $normalisedArray);
    }
}