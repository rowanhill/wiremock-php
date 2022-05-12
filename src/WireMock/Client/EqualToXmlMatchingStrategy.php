<?php
namespace WireMock\Client;

class EqualToXmlMatchingStrategy extends ValueMatchingStrategy
{
    /** @var bool */
    private $enablePlaceholders;
    /** @var string|null */
    private $placeholderOpeningDelimiterRegex;
    /** @var string|null */
    private $placeholderClosingDelimiterRegex;
    /** @var string[]|null */
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

    /**
     * @return bool
     */
    public function isEnablePlaceholders(): bool
    {
        return $this->enablePlaceholders;
    }

    /**
     * @return string|null
     */
    public function getPlaceholderOpeningDelimiterRegex(): ?string
    {
        return $this->placeholderOpeningDelimiterRegex;
    }

    /**
     * @return string|null
     */
    public function getPlaceholderClosingDelimiterRegex(): ?string
    {
        return $this->placeholderClosingDelimiterRegex;
    }

    /**
     * @return string[]|null
     */
    public function getExemptedComparisons(): ?array
    {
        return $this->exemptedComparisons;
    }
}