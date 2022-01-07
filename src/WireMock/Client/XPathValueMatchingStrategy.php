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

    /**
     * @return array
     */
    public function toArray()
    {
        if (!$this->_valueMatchingStrategy) {
            $array = parent::toArray();
            if (!empty($this->_xPathNamespaces)) {
                $array['xPathNamespaces'] = $this->_xPathNamespaces;
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

    public static function fromArray(array $array)
    {
        if (is_array($array['matchesXPath'])) {
            $matchingValue = $array['matchesXPath']['expression'];
            $matchingStrategyArray = $array['matchesXPath'];
            unset($matchingStrategyArray['expression']);
            $matchingStrategy = ValueMatchingStrategy::fromArray($matchingStrategyArray);
            return new self($matchingValue, $matchingStrategy);
        } else {
            $matchingValue = $array['matchesXPath'];
            $result = new self($matchingValue);
            if (isset($array['xPathNamespaces'])) {
                $namespaces = $array['xPathNamespaces'];
                foreach ($namespaces as $name => $uri) {
                    $result->withXPathNamespace($name, $uri);
                }
            }
            return $result;
        }
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