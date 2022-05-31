<?php

namespace WireMock\Client;

class DateTimeMatchExpectedOffset
{
    /**
     * @var int
     * @serde-name expectedOffset
     */
    private $amount;
    /**
     * @var string One of the offset unit constants on DateTimeMatchingStrategy
     * @serde-name expectedOffsetUnit
     */
    private $unit;

    /**
     * @param int $amount
     * @param string $unit
     */
    public function __construct(int $amount, string $unit)
    {
        $this->amount = $amount;
        $this->unit = $unit;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }
}