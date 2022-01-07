<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class MatchResult implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

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
}