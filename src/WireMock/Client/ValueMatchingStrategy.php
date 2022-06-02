<?php

namespace WireMock\Client;

use WireMock\Serde\ClassDiscriminator;
use WireMock\Serde\MappingProvider;
use WireMock\Serde\SerializationException;

class ValueMatchingStrategy implements MappingProvider
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
    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function matchingValueNames(): array { return array_keys(self::$subclassByMatchingType); }

    /** @var string */
    protected $matchingType;
    /**
     * @var string|boolean|ValueMatchingStrategy[]
     * @serde-named-by matchingType
     * @serde-possible-names matchingValueNames
     */
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
