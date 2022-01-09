<?php

namespace WireMock\Client;

class FindNearMissesResult
{
    /** @var NearMiss[] */
    private $nearMisses;

    /**
     * @param NearMiss[] $nearMisses
     */
    public function __construct(array $nearMisses)
    {
        $this->nearMisses = $nearMisses;
    }

    /**
     * @return NearMiss[]
     */
    public function getNearMisses()
    {
        return $this->nearMisses;
    }
}