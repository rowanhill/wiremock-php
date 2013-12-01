<?php

namespace WireMock\Client;

use WireMock\Matching\RequestPattern;
use WireMock\Matching\UrlMatchingStrategy;

class RequestPatternBuilder
{
    private $_method;
    private $_urlMatchingStrategy;
    private $_headers = array();

    /**
     * @param string $method
     * @param UrlMatchingStrategy $urlMatchingStrategy
     */
    function __construct($method, $urlMatchingStrategy)
    {
        $this->_method = $method;
        $this->_urlMatchingStrategy = $urlMatchingStrategy;
    }

    /**
     * @param string $headerName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withHeader($headerName, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_headers[$headerName] = $valueMatchingStrategy->toArray();
        return $this;
    }

    /**
     * @param string $headerName
     * @return RequestPatternBuilder
     */
    public function withoutHeader($headerName)
    {
        $this->_headers[$headerName] = array('absent' => true);
        return $this;
    }

    //TODO: withRequestBody

    /**
     * @return RequestPattern
     */
    function build()
    {
        $requestPattern = new RequestPattern($this->_method, $this->_urlMatchingStrategy);
        if (!empty($this->_headers)) {
            $requestPattern->setHeaders($this->_headers);
        }
        return $requestPattern;
    }
}