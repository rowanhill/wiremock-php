<?php
namespace WireMock\Client;

class EqualToXmlMatchingStrategy extends ValueMatchingStrategy
{
    private $_enablePlaceholders = false;
    private $_placeholderOpeningDelimiterRegex = null;
    private $_placeholderClosingDelimiterRegex = null;
    private $_exemptedComparisons = null;

    /**
     * @param string $matchingValue
     * @param bool $enablePlaceholders
     * @param string $placeholderOpeningDelimiterRegex
     * @param string $placeholderClosingDelimiterRegex
     */
    public function __construct(
        $matchingValue,
        $enablePlaceholders,
        $placeholderOpeningDelimiterRegex,
        $placeholderClosingDelimiterRegex
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
}