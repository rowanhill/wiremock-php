<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\PostNormalizationAmenderInterface;

class DateTimeMatchingStrategy extends ValueMatchingStrategy implements PostNormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    // Offset units
    const SECONDS = "seconds";
    const MINUTES = "minutes";
    const HOURS = "hours";
    const DAYS = "days";
    const MONTHS = "months";
    const YEARS = "years";

    // Truncation types
    const FIRST_SECOND_OF_MINUTE = "first second of minute";
    const FIRST_MINUTE_OF_HOUR = "first minute of hour";
    const FIRST_HOUR_OF_DAY = "first hour of day";
    const FIRST_DAY_OF_MONTH = "first day of month";
    const FIRST_DAY_OF_NEXT_MONTH = "first day of next month";
    const LAST_DAY_OF_MONTH = "last day of month";
    const FIRST_DAY_OF_YEAR = "first day of year";
    const FIRST_DAY_OF_NEXT_YEAR = "first day of next year";
    const LAST_DAY_OF_YEAR = "last day of year";

    private $_expectedOffset = null;
    private $_actualFormat = null;
    private $_truncateActual = null;
    private $_truncateExpected = null;

    public function __construct($matchingType, $dateTimeSpec)
    {
        parent::__construct($matchingType, $dateTimeSpec);
    }

    /**
     * @param int $amount
     * @param string $unit One of the offset unit constants on DateTimeMatchingStrategy
     * @return $this
     */
    public function expectedOffset($amount, $unit)
    {
        $this->_expectedOffset = array('amount' => $amount, 'unit' => $unit);
        return $this;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function actualFormat($format)
    {
        $this->_actualFormat = $format;
        return $this;
    }

    /**
     * @param string $truncationType One of the truncation type constants on DateTimeMatchingStrategy
     * @return $this
     */
    public function truncateExpected($truncationType)
    {
        $this->_truncateExpected = $truncationType;
        return $this;
    }

    /**
     * @param string $truncationType One of the truncation type constants on DateTimeMatchingStrategy
     * @return $this
     */
    public function truncateActual($truncationType)
    {
        $this->_truncateActual = $truncationType;
        return $this;
    }

    public function toArray()
    {
        $array = parent::toArray();
        if ($this->_expectedOffset) {
            $array['expectedOffset'] = $this->_expectedOffset['amount'];
            $array['expectedOffsetUnit'] = $this->_expectedOffset['unit'];
        }
        if ($this->_actualFormat) {
            $array['actualFormat'] = $this->_actualFormat;
        }
        if ($this->_truncateExpected) {
            $array['truncateExpected'] = $this->_truncateExpected;
        }
        if ($this->_truncateActual) {
            $array['truncateActual'] = $this->_truncateActual;
        }
        return $array;
    }

    /**
     * @param array $array
     * @param DateTimeMatchingStrategy $obj
     * @return DateTimeMatchingStrategy
     */
    public static function extendFromArray(array $array, self $obj)
    {
        if (isset($array['expectedOffset']) && isset($array['expectedOffsetUnit'])) {
            $obj->expectedOffset($array['expectedOffset'], $array['expectedOffsetUnit']);
        }
        if (isset($array['actualFormat'])) {
            $obj->actualFormat($array['actualFormat']);
        }
        if (isset($array['truncateExpected'])) {
            $obj->truncateExpected($array['truncateExpected']);
        }
        if (isset($array['truncateActual'])) {
            $obj->truncateActual($array['truncateActual']);
        }
        return $obj;
    }

    public static function before($dateTimeSpec)
    {
        return new self("before", $dateTimeSpec);
    }

    public static function equalToDateTime($dateTimeSpec)
    {
        return new self("equalToDateTime", $dateTimeSpec);
    }

    public static function after($dateTimeSpec)
    {
        return new self("after", $dateTimeSpec);
    }

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        $normalisedArray = parent::amendPostNormalisation($normalisedArray, $object);
        if (isset($normalisedArray['expectedOffset'])) {
            $expectedOffset = $normalisedArray['expectedOffset'];
            $normalisedArray['expectedOffset'] = $expectedOffset['amount'];
            $normalisedArray['expectedOffsetUnit'] = $expectedOffset['unit'];
        }
        return $normalisedArray;
    }
}