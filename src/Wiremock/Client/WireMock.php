<?php

namespace WireMock\Client;

use WireMock\Stubbing\StubMapping;

class WireMock
{
    /** @var string */
    private $_hostname;
    /** @var int */
    private $_port;
    /** @var HttpWait */
    private $_httpWait;
    /** @var Curl  */
    private $_curl;

    public static function create($hostname='localhost', $port=8080)
    {
        $httpWait = new HttpWait();
        $curl = new Curl();
        return new self($httpWait, $curl, $hostname, $port);
    }

    function __construct(HttpWait $httpWait, Curl $curl, $hostname='localhost', $port=8080)
    {
        $this->_hostname = $hostname;
        $this->_port = $port;
        $this->_httpWait = $httpWait;
        $this->_curl = $curl;
    }

    public function isAlive()
    {
        $url = $this->_makeUrl('__admin/');
        return $this->_httpWait->waitForServerToGive200($url);
    }

    public function stubFor(StubMapping $stubMapping)
    {
        $url = $this->_makeUrl('__admin/mappings/new');
        $this->_curl->post($url, $stubMapping->toArray());
    }

    private function _makeUrl($path)
    {
        return "http://$this->_hostname:$this->_port/$path";
    }

}