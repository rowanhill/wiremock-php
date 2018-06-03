<?php

namespace WireMock\Matching;

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

    /**
     * @param string $method
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @param array $headers
     * @param array $cookies
     * @param array $bodyPatterns
     * @param array $queryParameters
     */
    public function __construct(
        $method,
        $urlMatchingStrategy,
        $headers = null,
        $cookies = null,
        $bodyPatterns = null,
        $queryParameters = null
    ) {
        $this->_method = $method;
        $this->_urlMatchingStrategy = $urlMatchingStrategy;
        $this->_headers = $headers;
        $this->_cookies = $cookies;
        $this->_bodyPatterns = $bodyPatterns;
        $this->_queryParameters = $queryParameters;
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
