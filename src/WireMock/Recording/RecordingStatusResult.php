<?php

namespace WireMock\Recording;

class RecordingStatusResult
{
    const NEVER_STARTED = 'NeverStarted';
    const RECORDING = 'Recording';
    const STOPPED = 'Stopped';

    /** @var string */
    private $_status;

    /**
     * @param string $status
     */
    public function __construct($status)
    {
        $this->_status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @param array $array
     * @return RecordingStatusResult
     */
    public static function fromArray($array)
    {
        return new RecordingStatusResult($array['status']);
    }
}