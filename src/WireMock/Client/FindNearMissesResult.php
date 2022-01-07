<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class FindNearMissesResult implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var NearMiss[] */
    private $_nearMisses;

    /**
     * @param NearMiss[] $nearMisses
     */
    public function __construct(array $nearMisses)
    {
        $this->_nearMisses = $nearMisses;
    }

    /**
     * @return NearMiss[]
     */
    public function getNearMisses()
    {
        return $this->_nearMisses;
    }
}