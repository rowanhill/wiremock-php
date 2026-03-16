<?php

namespace WireMock\Client;

class LoggedResponse
{
    /** @var int */
    private $status;
    /** @var array<string, int|string> */
    private $headers;
    /** @var string */
    private $body;

    /**
     * @param int $status
     * @param array<int|string, mixed> $headers
     * @param string $body
     */
    public function __construct($status, $body, $headers = [])
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}