<?php

namespace WireMock\Recording;

class RecordingStatusResult
{
    const NEVER_STARTED = 'NeverStarted';
    const RECORDING = 'Recording';
    const STOPPED = 'Stopped';

    /** @var string */
    private $status;

    /**
     * @param string $status
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}