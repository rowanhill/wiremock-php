<?php

namespace WireMock\Matching;

use WireMock\Client\BasicCredentials;
use WireMock\Client\MultipartValuePattern;
use WireMock\Client\ValueMatchingStrategy;

class RequestPattern
{
    /** @var string */
    private $method;
    /**
     * @var UrlMatchingStrategy|null
     * @serde-unwrapped
     */
    private $urlMatchingStrategy;
    /** @var array<string, ValueMatchingStrategy>|null */
    private $headers;
    /** @var array<string, ValueMatchingStrategy>|null */
    private $cookies;
    /** @var array<string, ValueMatchingStrategy>|null */
    private $queryParameters;
    /** @var ValueMatchingStrategy[]|null */
    private $bodyPatterns;
    /** @var MultipartValuePattern[]|null */
    private $multipartPatterns;
    /** @var ?BasicCredentials */
    private $basicAuthCredentials;
    /** @var ?CustomMatcherDefinition */
    private $customMatcher;
    /** @var ?ValueMatchingStrategy */
    private $host;

    /**
     * @param string $method
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @param array<string, ValueMatchingStrategy>|null $headers
     * @param array<string, ValueMatchingStrategy>|null $cookies
     * @param ValueMatchingStrategy[]|null $bodyPatterns
     * @param ValueMatchingStrategy[] $multipartPatterns
     * @param array<string, ValueMatchingStrategy>|null $queryParameters
     * @param BasicCredentials $basicCredentials
     * @param CustomMatcherDefinition $customMatcherDefinition
     * @param ValueMatchingStrategy $hostPattern
     */
    public function __construct(
        $method,
        $urlMatchingStrategy,
        $headers = null,
        $cookies = null,
        $bodyPatterns = null,
        $multipartPatterns = null,
        $queryParameters = null,
        $basicCredentials = null,
        $customMatcherDefinition = null,
        $hostPattern = null
    ) {
        $this->method = $method;
        $this->urlMatchingStrategy = $urlMatchingStrategy;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->bodyPatterns = $bodyPatterns;
        $this->queryParameters = $queryParameters;
        $this->basicAuthCredentials = $basicCredentials;
        $this->multipartPatterns = $multipartPatterns;
        $this->customMatcher = $customMatcherDefinition;
        $this->host = $hostPattern;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return UrlMatchingStrategy
     */
    public function getUrlMatchingStrategy()
    {
        return $this->urlMatchingStrategy;
    }

    /**
     * @return array<string, ValueMatchingStrategy>
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array<string, ValueMatchingStrategy>
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return array<string, ValueMatchingStrategy>
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * @return ValueMatchingStrategy[]
     */
    public function getBodyPatterns()
    {
        return $this->bodyPatterns;
    }

    /**
     * @return ValueMatchingStrategy[]
     */
    public function getMultipartPatterns()
    {
        return $this->multipartPatterns;
    }

    /**
     * @return BasicCredentials
     */
    public function getBasicAuthCredentials()
    {
        return $this->basicAuthCredentials;
    }

    /**
     * @return CustomMatcherDefinition
     */
    public function getCustomMatcher()
    {
        return $this->customMatcher;
    }

    /**
     * @return ValueMatchingStrategy|null
     */
    public function getHost()
    {
        return $this->host;
    }
}
