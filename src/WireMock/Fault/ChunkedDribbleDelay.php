<?php

namespace WireMock\Fault;

class ChunkedDribbleDelay
{
    /** @var int */
    private $_numberOfChunks;
    /** @var int */
    private $_totalDurationMillis;

    /**
     * @param int $numberOfChunks
     * @param int $totalDurationMillis
     */
    public function __construct($numberOfChunks, $totalDurationMillis)
    {
        $this->_numberOfChunks = $numberOfChunks;
        $this->_totalDurationMillis = $totalDurationMillis;
    }

    /**
     * @return int
     */
    public function getNumberOfChunks()
    {
        return $this->_numberOfChunks;
    }

    /**
     * @return int
     */
    public function getTotalDurationMillis()
    {
        return $this->_totalDurationMillis;
    }

    public function toArray()
    {
        return array(
            'numberOfChunks' => $this->_numberOfChunks,
            'totalDuration' => $this->_totalDurationMillis
        );
    }

    public static function fromArray(array $array)
    {
        return new ChunkedDribbleDelay($array['numberOfChunks'], $array['totalDuration']);
    }
}