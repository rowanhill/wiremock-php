<?php

namespace WireMock\Fault;

use WireMock\Serde\PostNormalizationAmenderInterface;

class LogNormal implements DelayDistribution, PostNormalizationAmenderInterface
{
    /** @var float */
    private $_median;
    /** @var float */
    private $_sigma;

    /**
     * @param float $median
     * @param float $sigma
     */
    public function __construct($median, $sigma)
    {
        $this->_median = $median;
        $this->_sigma = $sigma;
    }

    /**
     * @return float
     */
    public function getMedian()
    {
        return $this->_median;
    }

    /**
     * @return float
     */
    public function getSigma()
    {
        return $this->_sigma;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'type' => 'lognormal',
            'median' => $this->_median,
            'sigma' => $this->_sigma
        );
    }

    /**
     * @param array $array
     * @return LogNormal
     */
    public static function fromArray(array $array)
    {
        return new LogNormal($array['median'], $array['sigma']);
    }

    public static function amendNormalisation(array $normalisedArray, $object): array
    {
        $normalisedArray['type'] = 'lognormal';
        return $normalisedArray;
    }
}