<?php

namespace WireMock\Client;

use WireMock\Matching\RequestPattern;
use WireMock\Matching\UrlMatchingStrategy;

class RequestPatternBuilder
{
    private $_method;
    private $_urlMatchingStrategy;
    private $_headers = array();
    private $_queryParameters = array();
    private $_bodyPatterns = array();

    /**
     * @param string $method
     * @param UrlMatchingStrategy $urlMatchingStrategy
     */
    public function __construct($method, $urlMatchingStrategy)
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

    /**
     * @param string $name
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withQueryParameter($name, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_queryParameters[$name] = $valueMatchingStrategy->toArray();
        return $this;
    }

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withRequestBody(ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_bodyPatterns[] = $valueMatchingStrategy->toArray();
        return $this;
    }

    /**
     * @return RequestPattern
     */
    public function build()
    {
        $requestPattern = new RequestPattern($this->_method, $this->_urlMatchingStrategy);
        if (!empty($this->_headers)) {
            $requestPattern->setHeaders($this->_headers);
        }
        if (!empty($this->_bodyPatterns)) {
            $requestPattern->setBodyPatterns($this->_bodyPatterns);
        }
        if (!empty($this->_queryParameters)) {
            $requestPattern->setQueryParameters($this->_queryParameters);
        }
        return $requestPattern;
    }
}
