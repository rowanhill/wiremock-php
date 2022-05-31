<?php

namespace WireMock\Client;

class DateTimeMatchingStrategy extends ValueMatchingStrategy
{
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

    /**
     * @var DateTimeMatchExpectedOffset
     * @serde-unwrapped
     */
    private $expectedOffset = null;
    /** @var string|null */
    private $actualFormat = null;
    /** @var string|null */
    private $truncateActual = null;
    /** @var string|null */
    private $truncateExpected = null;

    public function __construct($matchingType, $matchingValue)
    {
        parent::__construct($matchingType, $matchingValue);
    }

    /**
     * @param int $amount
     * @param string $unit One of the offset unit constants on DateTimeMatchingStrategy
     * @return $this
     */
    public function expectedOffset($amount, $unit)
    {
        $this->expectedOffset = new DateTimeMatchExpectedOffset($amount, $unit);
        return $this;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function actualFormat($format)
    {
        $this->actualFormat = $format;
        return $this;
    }

    /**
     * @param string $truncationType One of the truncation type constants on DateTimeMatchingStrategy
     * @return $this
     */
    public function truncateExpected($truncationType)
    {
        $this->truncateExpected = $truncationType;
        return $this;
    }

    /**
     * @param string $truncationType One of the truncation type constants on DateTimeMatchingStrategy
     * @return $this
     */
    public function truncateActual($truncationType)
    {
        $this->truncateActual = $truncationType;
        return $this;
    }

    /**
     * @return DateTimeMatchExpectedOffset|null
     */
    public function getExpectedOffset(): ?DateTimeMatchExpectedOffset
    {
        return $this->expectedOffset;
    }

    /**
     * @return string|null
     */
    public function getActualFormat(): ?string
    {
        return $this->actualFormat;
    }

    /**
     * @return string|null
     */
    public function getTruncateActual(): ?string
    {
        return $this->truncateActual;
    }

    /**
     * @return string|null
     */
    public function getTruncateExpected(): ?string
    {
        return $this->truncateExpected;
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
}