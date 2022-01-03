<?php

namespace WireMock\Client;

use WireMock\Serde\PostNormalizationAmenderInterface;

class ValueMatchingStrategy implements PostNormalizationAmenderInterface
{
    /** @var string */
    protected $_matchingType;
    /** @var string|boolean|ValueMatchingStrategy[] */
    protected $_matchingValue;

    public function __construct($matchingType, $matchingValue)
    {
        $this->_matchingType = $matchingType;
        $this->_matchingValue = $matchingValue;
    }

    /**
     * @return string
     */
    public function getMatchingType(): string
    {
        return $this->_matchingType;
    }

    /**
     * @return bool|string|ValueMatchingStrategy[]
     */
    public function getMatchingValue()
    {
        return $this->_matchingValue;
    }

    public function toArray()
    {
        return array($this->_matchingType => $this->_matchingValue);
    }

    public function and(ValueMatchingStrategy $other)
    {
        return LogicalOperatorMatchingStrategy::andAll($this, $other);
    }

    public function or(ValueMatchingStrategy $other)
    {
        return LogicalOperatorMatchingStrategy::orAll($this, $other);
    }

    /**
     * @throws \Exception Thrown if no key in the array is a known matching strategy
     */
    public static function fromArray(array $array)
    {
        foreach ($array as $key => $value) {
            switch ($key) {
                case 'absent':
                case 'binaryEqualTo':
                case 'contains':
                case 'matches':
                case 'doesNotMatch':
                    return new ValueMatchingStrategy($key, $value);
                case 'before':
                case 'equalToDateTime':
                case 'after':
                    $obj = new DateTimeMatchingStrategy($key, $value);
                    return DateTimeMatchingStrategy::extendFromArray($array, $obj);
                case 'equalTo':
                    return EqualToMatchingStrategy::fromArray($array);
                case 'matchesXPath':
                    return XPathValueMatchingStrategy::fromArray($array);
                case 'equalToXml':
                    return EqualToXmlMatchingStrategy::fromArray($array);
                case 'matchesJsonPath':
                    return JsonPathValueMatchingStrategy::fromArray($array);
                case 'equalToJson':
                    return JsonValueMatchingStrategy::fromArray($array);
                case 'and':
                case 'or':
                    return LogicalOperatorMatchingStrategy::fromArrayForOperator($array, $key);
            }
        }
        throw new \Exception("Could not denormalise array to ValueMatchingStrategy");
    }

    public static function amendNormalisation(array $normalisedArray, $object): array
    {
        $matchingType = $normalisedArray['matchingType'];
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingType']);
        unset($normalisedArray['matchingValue']);

        $normalisedArray[$matchingType] = $matchingValue;

        return $normalisedArray;
    }
}
