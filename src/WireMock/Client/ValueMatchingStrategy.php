<?php

namespace WireMock\Client;

use WireMock\Serde\ClassDiscriminator;
use WireMock\Serde\MappingProvider;
use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;
use WireMock\Serde\SerializationException;

class ValueMatchingStrategy implements PostNormalizationAmenderInterface, PreDenormalizationAmenderInterface, MappingProvider
{
    private static $subclassByMatchingType = [
        'absent' => ValueMatchingStrategy::class,
        'binaryEqualTo' => ValueMatchingStrategy::class,
        'contains' => ValueMatchingStrategy::class,
        'matches' => ValueMatchingStrategy::class,
        'doesNotMatch' => ValueMatchingStrategy::class,

        'before' => DateTimeMatchingStrategy::class,
        'equalToDateTime' => DateTimeMatchingStrategy::class,
        'after' => DateTimeMatchingStrategy::class,

        'equalTo' => EqualToMatchingStrategy::class,

        'matchesXPath' => XPathValueMatchingStrategy::class,

        'equalToXml' => EqualToXmlMatchingStrategy::class,

        'matchesJsonPath' => JsonPathValueMatchingStrategy::class,

        'equalToJson' => JsonValueMatchingStrategy::class,

        'and' => LogicalOperatorMatchingStrategy::class,
        'or' => LogicalOperatorMatchingStrategy::class,
    ];

    /** @var string */
    protected $matchingType;
    /** @var string|boolean|ValueMatchingStrategy[] */
    protected $matchingValue;

    public function __construct($matchingType, $matchingValue)
    {
        $this->matchingType = $matchingType;
        $this->matchingValue = $matchingValue;
    }

    /**
     * @return string
     */
    public function getMatchingType(): string
    {
        return $this->matchingType;
    }

    /**
     * @return bool|string|ValueMatchingStrategy[]
     */
    public function getMatchingValue()
    {
        return $this->matchingValue;
    }

    public function and(ValueMatchingStrategy $other)
    {
        return LogicalOperatorMatchingStrategy::andAll($this, $other);
    }

    public function or(ValueMatchingStrategy $other)
    {
        return LogicalOperatorMatchingStrategy::orAll($this, $other);
    }

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        $matchingType = $normalisedArray['matchingType'];
        $matchingValue = $normalisedArray['matchingValue'];
        unset($normalisedArray['matchingType']);
        unset($normalisedArray['matchingValue']);

        $normalisedArray[$matchingType] = $matchingValue;

        return $normalisedArray;
    }

    public static function amendPreDenormalisation(array $normalisedArray): array
    {
        foreach ($normalisedArray as $key => $value) {
            if (isset(self::$subclassByMatchingType[$key])) {
                $normalisedArray['matchingType'] = $key;
                $normalisedArray['matchingValue'] = $normalisedArray[$key];
                unset($normalisedArray[$key]);
                break;
            }
        }
        return $normalisedArray;
    }

    static function getDiscriminatorMapping(): ClassDiscriminator
    {
        return new class(self::$subclassByMatchingType) implements ClassDiscriminator {
            private $subclassByMatchingType;
            public function __construct($subclassByMatchingType)
            {
                $this->subclassByMatchingType = $subclassByMatchingType;
            }

            function getDiscriminatedType($data): string
            {
                foreach ($data as $key => $value) {
                    if (isset($this->subclassByMatchingType[$key])) {
                        return $this->subclassByMatchingType[$key];
                    }
                }
                throw new SerializationException("Cannot discriminate subclass of ValueMatchingStrategy");
            }
        };
    }
}
