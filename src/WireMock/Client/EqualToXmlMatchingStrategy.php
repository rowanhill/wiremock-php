<?php
namespace WireMock\Client;

class EqualToXmlMatchingStrategy extends ValueMatchingStrategy
{
    private $enablePlaceholders;
    private $placeholderOpeningDelimiterRegex;
    private $placeholderClosingDelimiterRegex;
    private $exemptedComparisons = null;

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
        $this->enablePlaceholders = $enablePlaceholders;
        $this->placeholderOpeningDelimiterRegex = $placeholderOpeningDelimiterRegex;
        $this->placeholderClosingDelimiterRegex = $placeholderClosingDelimiterRegex;
    }

    /**
     * @param string $comparisonTypes...
     * @return $this
     */
    public function exemptingComparisons($comparisonTypes)
    {
        $comparisonTypes = func_get_args();
        $this->exemptedComparisons = $comparisonTypes;
        return $this;
    }
}