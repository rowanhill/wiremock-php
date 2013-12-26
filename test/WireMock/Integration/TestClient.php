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

    function get($path, array $headers=array())
    {
        $ch = curl_init($this->_makeUrl($path));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = $this->_makeTimedRequest($ch);

        curl_close($ch);

        return $result;
    }

    function post($path, $body)
    {
        $ch = curl_init($this->_makeUrl($path));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $result = $this->_makeTimedRequest($ch);

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

    private function _makeUrl($path)
    {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        return "http://$this->_hostname:$this->_port$path";
    }

    private function _makeTimedRequest($ch)
    {
        $startTime = microtime(true);
        $result = curl_exec($ch);
        $endTime = microtime(true);
        $this->_lastRequestTimeMillis = ($endTime - $startTime) * 1000;

        return $result;
    }
}