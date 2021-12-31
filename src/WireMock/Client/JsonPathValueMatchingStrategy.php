<?php

namespace WireMock\Client;

class JsonPathValueMatchingStrategy extends ValueMatchingStrategy
{
    /** @var ValueMatchingStrategy */
    private $_valueMatchingStrategy;

    /**
     * @param string $jsonPath
     * @param ValueMatchingStrategy $valueMatchingStrategy
     */
    public function __construct($jsonPath, $valueMatchingStrategy = null)
    {
        parent::__construct('matchesJsonPath', $jsonPath);
        $this->_valueMatchingStrategy = $valueMatchingStrategy;
    }

    public function toArray()
    {
        if (!$this->_valueMatchingStrategy) {
            return parent::toArray();
        } else {
            return array(
                'matchesJsonPath' => array_merge(
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
        if (is_array($array['matchesJsonPath'])) {
            $matchingValue = $array['matchesJsonPath']['expression'];
            $matchingStrategyArray = $array['matchesJsonPath'];
            unset($matchingStrategyArray['expression']);
            $matchingStrategy = ValueMatchingStrategy::fromArray($matchingStrategyArray);
            return new self($matchingValue, $matchingStrategy);
        } else {
            $matchingValue = $array['matchesJsonPath'];
            return new self($matchingValue);
        }
    }
}