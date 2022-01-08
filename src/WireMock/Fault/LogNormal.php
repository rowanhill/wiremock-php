<?php

namespace WireMock\Fault;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\PostNormalizationAmenderInterface;

class LogNormal implements DelayDistribution, PostNormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var float */
    private $median;
    /** @var float */
    private $sigma;

    /**
     * @param float $median
     * @param float $sigma
     */
    public function __construct($median, $sigma)
    {
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

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        $normalisedArray['type'] = 'lognormal';
        return $normalisedArray;
    }
}