<?php

namespace WireMock\Fault;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\NormalizerUtils;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;

class ChunkedDribbleDelay implements PostNormalizationAmenderInterface, PreDenormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var int */
    private $numberOfChunks;
    /** @var int */
    private $totalDurationMillis;

    /**
     * @param int $numberOfChunks
     * @param int $totalDurationMillis
     */
    public function __construct($numberOfChunks, $totalDurationMillis)
    {
        $this->numberOfChunks = $numberOfChunks;
        $this->totalDurationMillis = $totalDurationMillis;
    }

    /**
     * @return int
     */
    public function getNumberOfChunks()
    {
        return $this->numberOfChunks;
    }

    /**
     * @return int
     */
    public function getTotalDurationMillis()
    {
        return $this->totalDurationMillis;
    }

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        NormalizerUtils::renameKey($normalisedArray, 'totalDurationMillis', 'totalDuration');
        return $normalisedArray;
    }

    public static function amendPreNormalisation(array $normalisedArray): array
    {
        NormalizerUtils::renameKey($normalisedArray, 'totalDuration', 'totalDurationMillis');
        return $normalisedArray;
    }
}