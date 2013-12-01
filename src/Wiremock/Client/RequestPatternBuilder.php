<?php

namespace WireMock\Client;

use WireMock\Matching\RequestPattern;
use WireMock\Matching\UrlMatchingStrategy;

class RequestPatternBuilder
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

    //TODO: withHeader, withoutHeader
    //TODO: withRequestBody

    /**
     * @return RequestPattern
     */
    function build()
    {
        return new RequestPattern($this->_method, $this->_urlMatchingStrategy);
    }
}