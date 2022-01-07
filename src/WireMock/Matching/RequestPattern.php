<?php

namespace WireMock\Matching;

use Symfony\Component\Serializer\Serializer;
use WireMock\Client\BasicCredentials;
use WireMock\Client\MultipartValuePattern;
use WireMock\Client\ValueMatchingStrategy;
use WireMock\Serde\NormalizerUtils;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PostNormalizationAmenderInterface;

class RequestPattern implements PostNormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    /** @var string */
    private $_method;
    /** @var UrlMatchingStrategy  */
    private $_urlMatchingStrategy;
    /** @var \array<string, ValueMatchingStrategy> */
    private $_headers;
    /** @var \array<ValueMatchingStrategy> */
    private $_cookies;
    /** @var \array<ValueMatchingStrategy> */
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
     * @param array<string, ValueMatchingStrategy> $headers
     * @param array<string, ValueMatchingStrategy> $cookies
     * @param ValueMatchingStrategy[] $bodyPatterns
     * @param ValueMatchingStrategy[] $multipartPatterns
     * @param array<string, ValueMatchingStrategy> $queryParameters
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
     * @return array<string, ValueMatchingStrategy>
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return array<string, ValueMatchingStrategy>
     */
    public function getCookies()
    {
        return $this->_cookies;
    }

    /**
     * @return array<string, ValueMatchingStrategy>
     */
    public function getQueryParameters()
    {
        return $this->_queryParameters;
    }

    /**
     * @return ValueMatchingStrategy[]
     */
    public function getBodyPatterns()
    {
        return $this->_bodyPatterns;
    }

    /**
     * @return ValueMatchingStrategy[]
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

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        NormalizerUtils::inline($normalisedArray, 'urlMatchingStrategy');
        return $normalisedArray;
    }

    static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        $method = $normalisedArray['method'];
        unset($normalisedArray['method']);

        $urlMatchingStrategy = $serializer->denormalize($normalisedArray, UrlMatchingStrategy::class, $format, $context);

        return new ObjectToPopulateResult(new self($method, $urlMatchingStrategy), $normalisedArray);
    }
}
