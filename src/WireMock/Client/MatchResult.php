<?php

namespace WireMock\Client;

class MatchResult
{
    private $distance;

    /**
     * @param float $distance
     */
    public function __construct($distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }
}