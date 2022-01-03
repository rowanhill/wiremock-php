<?php

namespace WireMock\Matching;

use WireMock\Client\BasicCredentials;
use WireMock\Client\MultipartValuePattern;
use WireMock\Client\ValueMatchingStrategy;
use WireMock\Serde\NormalizerUtils;
use WireMock\Serde\PostNormalizationAmenderInterface;

class RequestPattern implements PostNormalizationAmenderInterface
{
    /** @var string */
    private $_method;
    /** @var UrlMatchingStrategy  */
    private $_urlMatchingStrategy;
    /** @var ValueMatchingStrategy[] */
    private $_headers;
    /** @var ValueMatchingStrategy[] */
    private $_cookies;
    /** @var ValueMatchingStrategy[] */
    private $_queryParameters;
    /** @var ValueMatchingStrategy[] */
    private $_bodyPatterns;
    /** @var null|MultipartValuePattern[] */
    private $_multipartPatterns;
    /** @var BasicCredentials */
    private $_basicAuthCredentials;
    /** @var CustomMatcherDefinition */
    private $_customMatcher;
    /** @var ValueMatchingStrategy */
    private $_host;

    /**
     * @param string $method
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @param ValueMatchingStrategy[] $headers
     * @param ValueMatchingStrategy[] $cookies
     * @param ValueMatchingStrategy[] $bodyPatterns
     * @param ValueMatchingStrategy[] $multipartPatterns
     * @param ValueMatchingStrategy[] $queryParameters
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
        $this->_method = $method;
        $this->_urlMatchingStrategy = $urlMatchingStrategy;
        $this->_headers = $headers;
        $this->_cookies = $cookies;
        $this->_bodyPatterns = $bodyPatterns;
        $this->_queryParameters = $queryParameters;
        $this->_basicAuthCredentials = $basicCredentials;
        $this->_multipartPatterns = $multipartPatterns;
        $this->_customMatcher = $customMatcherDefinition;
        $this->_host = $hostPattern;
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
     * @return array
     */
    public function getMultipartPatterns()
    {
        return $this->_multipartPatterns;
    }

    /**
     * @return BasicCredentials
     */
    public function getBasicAuthCredentials()
    {
        return $this->_basicAuthCredentials;
    }

    /**
     * @return CustomMatcherDefinition
     */
    public function getCustomMatcher()
    {
        return $this->_customMatcher;
    }

    /**
     * @return ValueMatchingStrategy|null
     */
    public function getHost()
    {
        return $this->_host;
    }

    public function toArray()
    {
        $array = array();
        if ($this->_method) {
            $array['method'] = $this->_method;
        }
        if ($this->_urlMatchingStrategy) {
            $array = array_merge($array, $this->_urlMatchingStrategy->toArray());
        }
        if ($this->_headers) {
            $array['headers'] = array_map(function($h) { if (is_array($h)) { return $h; } else { return $h->toArray(); } }, $this->_headers);
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
        if ($this->_multipartPatterns) {
            $array['multipartPatterns'] = $this->_multipartPatterns;
        }
        if ($this->_basicAuthCredentials) {
            $array['basicAuthCredentials'] = $this->_basicAuthCredentials->toArray();
        }
        if ($this->_customMatcher) {
            $array['customMatcher'] = $this->_customMatcher->toArray();
        }
        if ($this->_host) {
            $array['host'] = $this->_host->toArray();
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
            isset($array['headers']) ? array_map(function($value) { return ValueMatchingStrategy::fromArray($value); }, $array['headers']) : null,
            isset($array['cookies']) ? $array['cookies'] : null,
            isset($array['bodyPatterns']) ? $array['bodyPatterns'] : null,
            isset($array['multipartPatterns']) ? $array['multipartPatterns'] : null,
            isset($array['queryParameters']) ? $array['queryParameters'] : null,
            isset($array['basicAuthCredentials']) ? BasicCredentials::fromArray($array['basicAuthCredentials']) : null,
            isset($array['customMatcher']) ? CustomMatcherDefinition::fromArray($array['customMatcher']) : null,
            isset($array['host']) ? ValueMatchingStrategy::fromArray($array['host']) : null
        );
    }

    public static function amendNormalisation(array $normalisedArray, $object): array
    {
        NormalizerUtils::inline($normalisedArray, 'urlMatchingStrategy');
        return $normalisedArray;
    }
}
