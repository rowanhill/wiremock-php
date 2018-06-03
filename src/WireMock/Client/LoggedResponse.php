<?php

namespace WireMock\Client;

class LoggedResponse
{
    /** @var int */
    private $_status;
    /** @var array */
    private $_headers;
    /** @var string */
    private $_body;

    /**
     * @param int $status
     * @param array $headers
     * @param string $body
     */
    public function __construct($status, $headers, $body)
    {
        $this->_status = $status;
        $this->_headers = $headers;
        $this->_body = $body;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    public static function fromArray(array $array)
    {
        return new LoggedResponse($array['status'], $array['headers'], base64_decode($array['bodyAsBase64']));
    }
}