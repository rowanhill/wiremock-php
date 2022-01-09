<?php

namespace WireMock\Fault;

class UniformDistribution extends DelayDistribution
{
    /** @var int */
    private $lower;
    /** @var int */
    private $upper;

    /**
     * @param int $lower
     * @param int $upper
     */
    public function __construct(int $lower, int $upper)
    {
        parent::__construct('uniform');
        $this->lower = $lower;
        $this->upper = $upper;
    }

    /**
     * @return int
     */
    public function getLower()
    {
        return $this->lower;
    }

    /**
     * @return int
     */
    public function getUpper()
    {
        return $this->upper;
    }
}