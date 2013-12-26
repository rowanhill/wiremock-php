<?php

namespace WireMock\Integration;

class TestClient
{
    private $_hostname;
    private $_port;
    private $_lastRequestTimeMillis;

    function __construct($_hostname, $_port)
    {
        $this->_hostname = $_hostname;
        $this->_port = $_port;
    }

    function get($path)
    {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        $ch = curl_init("http://$this->_hostname:$this->_port$path");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $startTime = microtime(true);
        $result = curl_exec($ch);
        $endTime = microtime(true);
        $this->_lastRequestTimeMillis = ($endTime - $startTime) * 1000;

        curl_close($ch);

        return $result;
    }

    /**
     * @return float|null
     */
    public function getLastRequestTimeMillis()
    {
        return $this->_lastRequestTimeMillis;
    }
}