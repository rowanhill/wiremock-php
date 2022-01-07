<?php

namespace WireMock\Client;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Serializer;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;

class ValueMatchingStrategy implements PostNormalizationAmenderInterface, PreDenormalizationAmenderInterface, ObjectToPopulateFactoryInterface
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
            $subclass = static::$subclassByMatchingType[$key];
            if ($subclass != null) {
                $normalisedArray['matchingType'] = $key;
                $normalisedArray['matchingValue'] = $normalisedArray[$key];
                unset($normalisedArray[$key]);

                if ($subclass != self::class) {
                    $method = new \ReflectionMethod($subclass, 'amendPreNormalisation');
                    if ($method->getDeclaringClass()->name == $subclass) {
                        $normalisedArray = $method->invoke(null, $normalisedArray);
                    }
                }

                return $normalisedArray;
            }
        }
        return $normalisedArray;
    }

    static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        $matchingType = $normalisedArray['matchingType'];
        $subclass = self::$subclassByMatchingType[$matchingType];
        if ($subclass != self::class) {
            $method = new \ReflectionMethod($subclass, 'createObjectToPopulate');
            if ($method->getDeclaringClass()->name == $subclass) {
                return $method->invoke(null, $normalisedArray, $serializer, $format, $context);
            } else {
                throw new NotNormalizableValueException(sprintf(
                    'Cannot create object to populate for class %s (with matching type %s) because it does not implement createObjectToPopulate',
                    $subclass,
                    $matchingType
                ));
            }
        } else {
            unset($normalisedArray['matchingType']);
            $matchingValue = $normalisedArray['matchingValue'];
            unset($normalisedArray['matchingValue']);

            return new ObjectToPopulateResult(new ValueMatchingStrategy($matchingType, $matchingValue), $normalisedArray);
        }
    }
}
