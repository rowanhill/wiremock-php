<?php

namespace WireMock\Client;

use WireMock\Matching\CustomMatcherDefinition;
use WireMock\Matching\RequestPattern;
use WireMock\Matching\UrlMatchingStrategy;

class RequestPatternBuilder
{
    private $_method;
    private $_urlMatchingStrategy;
    /** @var \array<string, ValueMatchingStrategy> */
    private $_headers = array();
    /** @var \array<string, ValueMatchingStrategy> */
    private $_cookies = array();
    /** @var \array<string, ValueMatchingStrategy> */
    private $_queryParameters = array();
    /** @var ValueMatchingStrategy[] */
    private $_bodyPatterns = array();
    /** @var MultipartValuePattern[] */
    private $_multipartPatterns = array();
    /** @var BasicCredentials */
    private $_basicCredentials;
    /** @var CustomMatcherDefinition */
    private $_customMatcherDefinition;
    /** @var ValueMatchingStrategy[] */
    private $_hostPattern;

    /**
     * @param string $methodOrCustomMatcherName
     * @param UrlMatchingStrategy|array $urlMatchingStrategyOrCustomParams
     */
    public function __construct($methodOrCustomMatcherName, $urlMatchingStrategyOrCustomParams)
    {
        if ($urlMatchingStrategyOrCustomParams instanceof UrlMatchingStrategy) {
            $this->_method = $methodOrCustomMatcherName;
            $this->_urlMatchingStrategy = $urlMatchingStrategyOrCustomParams;
        } else {
            $this->_customMatcherDefinition =
                new CustomMatcherDefinition($methodOrCustomMatcherName, $urlMatchingStrategyOrCustomParams);
        }
    }

    /**
     * @param string $headerName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withHeader($headerName, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_headers[$headerName] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param string $cookieName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withCookie($cookieName, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_cookies[$cookieName] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param string $headerName
     * @return RequestPatternBuilder
     */
    public function withoutHeader($headerName)
    {
        $this->_headers[$headerName] = new ValueMatchingStrategy('absent', true);
        return $this;
    }

    /**
     * @param string $name
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withQueryParam($name, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_queryParameters[$name] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withRequestBody(ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_bodyPatterns[] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param MultipartValuePattern $multipart
     * @return $this
     */
    public function withMultipartRequestBody($multipart)
    {
        $this->_multipartPatterns[] = $multipart;
        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     * @return RequestPatternBuilder
     */
    public function withBasicAuth($username, $password)
    {
        $this->_basicCredentials = new BasicCredentials($username, $password);
        return $this;
    }

    /**
     * @param string $customMatcherName
     * @param array $customParams
     * @return RequestPatternBuilder
     */
    public function withCustomMatcher($customMatcherName, $customParams)
    {
        $this->_customMatcherDefinition = new CustomMatcherDefinition($customMatcherName, $customParams);
        return $this;
    }

    /**
     * @param ValueMatchingStrategy $hostMatcher
     * @return $this
     */
    public function withHost($hostMatcher)
    {
        $this->_hostPattern = $hostMatcher;
        return $this;
    }

    /**
     * @return RequestPattern
     */
    public function build()
    {
        return new RequestPattern(
            $this->_method,
            $this->_urlMatchingStrategy,
            $this->_headers,
            $this->_cookies,
            $this->_bodyPatterns,
            $this->_multipartPatterns,
            $this->_queryParameters,
            $this->_basicCredentials,
            $this->_customMatcherDefinition,
            $this->_hostPattern
        );
    }
}
