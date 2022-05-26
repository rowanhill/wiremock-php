<?php

namespace WireMock\Fault;

class ChunkedDribbleDelay
{
    /** @var int */
    private $numberOfChunks;
    /**
     * @var int
     * @serde-name totalDuration
     */
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
}