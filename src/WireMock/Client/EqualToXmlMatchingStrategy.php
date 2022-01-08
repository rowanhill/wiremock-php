<?php
namespace WireMock\Client;

use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;

class EqualToXmlMatchingStrategy extends ValueMatchingStrategy implements ObjectToPopulateFactoryInterface
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

    public static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        unset($normalisedArray['matchingType']); // equalToXml
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingValue']);
        return new ObjectToPopulateResult(new self($matchingValue), $normalisedArray);
    }
}