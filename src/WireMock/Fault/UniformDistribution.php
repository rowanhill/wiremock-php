<?php

namespace WireMock\Fault;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\PostNormalizationAmenderInterface;

class UniformDistribution implements DelayDistribution, PostNormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var int */
    private $lower;
    /** @var int */
    private $upper;

    /**
     * @param int $lower
     * @param int $upper
     */
    public function __construct($lower, $upper)
    {
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

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        $normalisedArray['type'] = 'uniform';
        return $normalisedArray;
    }
}