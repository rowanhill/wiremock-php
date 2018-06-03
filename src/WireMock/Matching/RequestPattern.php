<?php

namespace WireMock\Matching;

use WireMock\Client\BasicCredentials;

class RequestPattern
{
    /** @var string */
    private $_method;
    /** @var UrlMatchingStrategy  */
    private $_urlMatchingStrategy;
    /** @var array */
    private $_headers;
    /** @var array */
    private $_cookies;
    /** @var array */
    private $_queryParameters;
    /** @var array */
    private $_bodyPatterns;
    /** @var BasicCredentials */
    private $_basicCredentials;

    /**
     * @param string $method
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @param array $headers
     * @param array $cookies
     * @param array $bodyPatterns
     * @param array $queryParameters
     * @param BasicCredentials $basicCredentials
     */
    public function __construct(
        $method,
        $urlMatchingStrategy,
        $headers = null,
        $cookies = null,
        $bodyPatterns = null,
        $queryParameters = null,
        $basicCredentials = null
    ) {
        $this->_method = $method;
        $this->_urlMatchingStrategy = $urlMatchingStrategy;
        $this->_headers = $headers;
        $this->_cookies = $cookies;
        $this->_bodyPatterns = $bodyPatterns;
        $this->_queryParameters = $queryParameters;
        $this->_basicCredentials = $basicCredentials;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @return UrlMatchingStrategy
     */
    public function getUrlMatchingStrategy()
    {
        return $this->_urlMatchingStrategy;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->_cookies;
    }

    /**
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->_queryParameters;
    }

    /**
     * @return array
     */
    public function getBodyPatterns()
    {
        return $this->_bodyPatterns;
    }

    /**
     * @return BasicCredentials
     */
    public function getBasicCredentials()
    {
        return $this->_basicCredentials;
    }

    public function toArray()
    {
        $array = array('method' => $this->_method);
        $array = array_merge($array, $this->_urlMatchingStrategy->toArray());
        if ($this->_headers) {
            $array['headers'] = $this->_headers;
        }
        if ($this->_cookies) {
            $array['cookies'] = $this->_cookies;
        }
        if ($this->_queryParameters) {
            $array['queryParameters'] = $this->_queryParameters;
        }
        if ($this->_bodyPatterns) {
            $array['bodyPatterns'] = $this->_bodyPatterns;
        }
        if ($this->_basicCredentials) {
            $array['basicAuthCredentials'] = $this->_basicCredentials->toArray();
        }
        return $array;
    }

    /**
     * @param array $array
     * @return RequestPattern
     * @throws \Exception
     */
    public static function fromArray(array $array)
    {
        return new RequestPattern(
            $array['method'],
            UrlMatchingStrategy::fromArray($array),
            isset($array['headers']) ?: null,
            isset($array['cookies']) ?: null,
            isset($array['queryParameters']) ?: null,
            isset($array['bodyPatterns']) ?: null
        );
    }
}
