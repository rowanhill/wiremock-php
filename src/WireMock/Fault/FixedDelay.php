<?php

namespace WireMock\Fault;

class FixedDelay extends DelayDistribution
{
    /** @var int */
    private $milliseconds;

    /**
     * @param int $milliseconds
     */
    public function __construct(int $milliseconds)
    {
        parent::__construct('fixed');
        $this->milliseconds = $milliseconds;
    }

    /**
     * @return int
     */
    public function getMilliseconds(): int
    {
        return $this->milliseconds;
    }
}