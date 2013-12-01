<?php

namespace WireMock\Matching;

class RequestPattern
{
    private $_method;
    private $_urlMatchingStrategy;

    /**
     * @param string $method
     * @param UrlMatchingStrategy $urlMatchingStrategy
     */
    function __construct($method, $urlMatchingStrategy)
    {
        $this->_method = $method;
        $this->_urlMatchingStrategy = $urlMatchingStrategy;
    }

    public function toArray()
    {
        $array = array('method' => $this->_method);
        $array = array_merge($array, $this->_urlMatchingStrategy->toArray());
        return $array;
    }
}