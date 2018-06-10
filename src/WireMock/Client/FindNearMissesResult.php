<?php

namespace WireMock\Client;

class FindNearMissesResult
{
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

    /**
     * @param array $array
     * @return FindNearMissesResult
     */
    public static function fromArray(array $array)
    {
        return new FindNearMissesResult(
            array_map(function($nm) { return NearMiss::fromArray($nm); }, $array['nearMisses'])
        );
    }
}