<?php

namespace WireMock\Client;

use WireMock\Serde\ClassDiscriminator;
use WireMock\Serde\SerializationException;

/**
 * @serde-discriminate-type getDiscriminatorMapping
 * @serde-possible-subtype ValueMatchingStrategy
 * @serde-possible-subtype DateTimeMatchingStrategy
 * @serde-possible-subtype EqualToMatchingStrategy
 * @serde-possible-subtype XPathValueMatchingStrategy
 * @serde-possible-subtype EqualToXmlMatchingStrategy
 * @serde-possible-subtype JsonPathValueMatchingStrategy
 * @serde-possible-subtype JsonValueMatchingStrategy
 * @serde-possible-subtype LogicalOperatorMatchingStrategy
 */
class ValueMatchingStrategy
{
    /** @var array<string, class-string> */
    private static $subclassByMatchingType = [
        'absent' => ValueMatchingStrategy::class,
        'binaryEqualTo' => ValueMatchingStrategy::class,
        'contains' => ValueMatchingStrategy::class,
        'doesNotContain' => ValueMatchingStrategy::class,
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
    /**
     * @return list<int|string>
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private static function matchingValueNames(): array // @phpstan-ignore method.unused (used by serde)
    {
        return array_keys(self::$subclassByMatchingType);
    }

    /** @var string */
    protected $matchingType;
    /**
     * @var string|boolean|ValueMatchingStrategy[]
     * @serde-named-by matchingType
     * @serde-possible-names matchingValueNames
     */
    protected $matchingValue;

    /**
     * @param string $matchingType
     * @param mixed $matchingValue
     */
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
     * @return boolean|string|ValueMatchingStrategy[]
     */
    public function getMatchingValue()
    {
        return $this->matchingValue;
    }

    public function and(ValueMatchingStrategy $other): LogicalOperatorMatchingStrategy
    {
        return LogicalOperatorMatchingStrategy::andAll($this, $other);
    }

    public function or(ValueMatchingStrategy $other): LogicalOperatorMatchingStrategy
    {
        return LogicalOperatorMatchingStrategy::orAll($this, $other);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function getDiscriminatorMapping(): ClassDiscriminator // @phpstan-ignore method.unused
    {
        return new class(self::$subclassByMatchingType) implements ClassDiscriminator {
            /** @var array<string, class-string> */
            private $subclassByMatchingType;
            /**
             * @param array<string, class-string> $subclassByMatchingType
             */
            public function __construct($subclassByMatchingType)
            {
                $this->subclassByMatchingType = $subclassByMatchingType;
            }

            /**
             * @param array<string, mixed> $data
             */
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
