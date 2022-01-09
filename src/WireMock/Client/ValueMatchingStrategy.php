<?php

namespace WireMock\Client;

use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use WireMock\Serde\MappingProvider;
use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;

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

    public static function amendPreNormalisation(array $normalisedArray): array
    {
        foreach ($normalisedArray as $key => $value) {
            if (isset(self::$subclassByMatchingType[$key])) {
                $subclass = self::$subclassByMatchingType[$key];
                $normalisedArray['matchingType'] = $key;
                $normalisedArray['matchingValue'] = $normalisedArray[$key];
                unset($normalisedArray[$key]);

                if ($subclass != self::class) {
                    $method = new \ReflectionMethod($subclass, 'amendPreNormalisation');
                    if ($method->getDeclaringClass()->name == $subclass) {
                        $normalisedArray = $method->invoke(null, $normalisedArray);
                    }
                }

                break;
            }
        }
        return $normalisedArray;
    }

    static function getDiscriminatorMapping(): ClassDiscriminatorMapping
    {
        return new ClassDiscriminatorMapping('matchingType', self::$subclassByMatchingType);
    }
}
