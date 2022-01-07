<?php
namespace WireMock\Client;

use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;

class EqualToXmlMatchingStrategy extends ValueMatchingStrategy implements ObjectToPopulateFactoryInterface
{
    private $_enablePlaceholders;
    private $_placeholderOpeningDelimiterRegex;
    private $_placeholderClosingDelimiterRegex;
    private $_exemptedComparisons = null;

    /**
     * @param string $matchingValue
     * @param bool $enablePlaceholders
     * @param string $placeholderOpeningDelimiterRegex
     * @param string $placeholderClosingDelimiterRegex
     */
    public function __construct(
        $matchingValue,
        $enablePlaceholders = false,
        $placeholderOpeningDelimiterRegex = null,
        $placeholderClosingDelimiterRegex = null
    ) {
        parent::__construct('equalToXml', $matchingValue);
        $this->_enablePlaceholders = $enablePlaceholders;
        $this->_placeholderOpeningDelimiterRegex = $placeholderOpeningDelimiterRegex;
        $this->_placeholderClosingDelimiterRegex = $placeholderClosingDelimiterRegex;
    }

    /**
     * @param string $comparisonTypes...
     * @return $this
     */
    public function exemptingComparisons($comparisonTypes)
    {
        $comparisonTypes = func_get_args();
        $this->_exemptedComparisons = $comparisonTypes;
        return $this;
    }

    public function toArray()
    {
        $array = parent::toArray();
        if ($this->_enablePlaceholders) {
            $array['enablePlaceholders'] = $this->_enablePlaceholders;
        }
        if ($this->_placeholderOpeningDelimiterRegex) {
            $array['placeholderOpeningDelimiterRegex'] = $this->_placeholderOpeningDelimiterRegex;
        }
        if ($this->_placeholderClosingDelimiterRegex) {
            $array['placeholderClosingDelimiterRegex'] = $this->_placeholderClosingDelimiterRegex;
        }
        if ($this->_exemptedComparisons) {
            $array['exemptedComparisons'] = $this->_exemptedComparisons;
        }
        return $array;
    }

    public static function fromArray(array $array)
    {
        $matchingValue = $array['equalToXml'];
        $enablePlaceholders = isset($array['enablePlaceholders']) && $array['enablePlaceholders'];
        $placeholderOpeningDelimiterRegex = isset($array['placeholderOpeningDelimiterRegex']) && $array['placeholderOpeningDelimiterRegex'];
        $placeholderClosingDelimiterRegex = isset($array['placeholderClosingDelimiterRegex']) && $array['placeholderClosingDelimiterRegex'];
        $result = new self($matchingValue, $enablePlaceholders, $placeholderOpeningDelimiterRegex, $placeholderClosingDelimiterRegex);
        if (isset($array['exemptedComparisons'])) {
            $result->exemptingComparisons(...$array['exemptedComparisons']);
        }
        return $result;
    }

    public static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        unset($normalisedArray['matchingType']); // equalToXml
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingValue']);
        return new ObjectToPopulateResult(new self($matchingValue), $normalisedArray);
    }
}