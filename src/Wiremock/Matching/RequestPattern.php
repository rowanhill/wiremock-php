<?php

namespace WireMock\Matching;

class RequestPattern
{
    private $_method;
    private $_urlMatchingStrategy;
    private $_headers;

    /**
     * @param string $method
     * @param UrlMatchingStrategy $urlMatchingStrategy
     */
    function __construct($method, $urlMatchingStrategy)
    {
        $this->_method = $method;
        $this->_urlMatchingStrategy = $urlMatchingStrategy;
    }

    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;
    }

    public function toArray()
    {
        $array = array('method' => $this->_method);
        $array = array_merge($array, $this->_urlMatchingStrategy->toArray());
        if ($this->_headers) {
            $array['headers'] = $this->_headers;
        }
        return $array;
    }
}