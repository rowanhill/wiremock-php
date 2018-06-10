<?php

namespace WireMock\Client;

class MatchResult
{
    private $_distance;

    /**
     * @param float $distance
     */
    public function __construct($distance)
    {
        $this->_distance = $distance;
    }

    /**
     * @return float
     */
    public function getDistance()
    {
        return $this->_distance;
    }

    /**
     * @param array $array
     * @return MatchResult
     */
    public static function fromArray(array $array)
    {
        return new MatchResult($array['distance']);
    }
}