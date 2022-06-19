<?php

namespace WireMock\Client;

use WireMock\Matching\CustomMatcherDefinition;
use WireMock\Matching\RequestPattern;
use WireMock\Matching\UrlMatchingStrategy;

class RequestPatternBuilder
{
    private $method;
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
    /** @var BasicCredentials */
    private $basicCredentials;
    /** @var CustomMatcherDefinition */
    private $customMatcherDefinition;
    /** @var ValueMatchingStrategy[] */
    private $hostPattern;

    /**
     * @param string $methodOrCustomMatcherName
     * @param UrlMatchingStrategy|array $urlMatchingStrategyOrCustomParams
     */
    public function __construct($methodOrCustomMatcherName, $urlMatchingStrategyOrCustomParams)
    {
        if ($urlMatchingStrategyOrCustomParams instanceof UrlMatchingStrategy) {
            $this->method = $methodOrCustomMatcherName;
            $this->urlMatchingStrategy = $urlMatchingStrategyOrCustomParams;
        } else {
            $this->customMatcherDefinition =
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
        $this->headers[$headerName] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param string $cookieName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withCookie($cookieName, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->cookies[$cookieName] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param string $headerName
     * @return RequestPatternBuilder
     */
    public function withoutHeader($headerName)
    {
        $this->withHeader($headerName, new ValueMatchingStrategy('absent', true));
        return $this;
    }

    /**
     * @param string $name
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withQueryParam($name, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->queryParameters[$name] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return RequestPatternBuilder
     */
    public function withRequestBody(ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->bodyPatterns[] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param MultipartValuePattern $multipart
     * @return $this
     */
    public function withMultipartRequestBody($multipart)
    {
        $this->multipartPatterns[] = $multipart;
        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     * @return RequestPatternBuilder
     */
    public function withBasicAuth($username, $password)
    {
        $this->basicCredentials = new BasicCredentials($username, $password);
        return $this;
    }

    /**
     * @param string $customMatcherName
     * @param array $customParams
     * @return RequestPatternBuilder
     */
    public function withCustomMatcher($customMatcherName, $customParams)
    {
        $this->customMatcherDefinition = new CustomMatcherDefinition($customMatcherName, $customParams);
        return $this;
    }

    /**
     * @param ValueMatchingStrategy $hostMatcher
     * @return $this
     */
    public function withHost($hostMatcher)
    {
        $this->hostPattern = $hostMatcher;
        return $this;
    }

    /**
     * @return RequestPattern
     */
    public function build()
    {
        return new RequestPattern(
            $this->method,
            $this->urlMatchingStrategy,
            $this->headers,
            $this->cookies,
            $this->bodyPatterns,
            $this->multipartPatterns,
            $this->queryParameters,
            $this->basicCredentials,
            $this->customMatcherDefinition,
            $this->hostPattern
        );
    }
}
