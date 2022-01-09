<?php

namespace WireMock\Fault;

class LogNormal extends DelayDistribution
{
    /** @var float */
    private $median;
    /** @var float */
    private $sigma;

    /**
     * @param float $median
     * @param float $sigma
     */
    public function __construct(float $median, float $sigma)
    {
        parent::__construct('lognormal');
        $this->median = $median;
        $this->sigma = $sigma;
    }

    /**
     * @return float
     */
    public function getMedian()
    {
        return $this->median;
    }

    /**
     * @return float
     */
    public function getSigma()
    {
        return $this->sigma;
    }
}