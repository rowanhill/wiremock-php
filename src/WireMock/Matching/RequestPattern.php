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
    private $method;
    /** @var UrlMatchingStrategy  */
    private $urlMatchingStrategy;
    /** @var \array<string, ValueMatchingStrategy> */
    private $headers;
    /** @var \array<ValueMatchingStrategy> */
    private $cookies;
    /** @var \array<ValueMatchingStrategy> */
    private $queryParameters;
    /** @var ValueMatchingStrategy[] */
    private $bodyPatterns;
    /** @var null|MultipartValuePattern[] */
    private $multipartPatterns;
    /** @var BasicCredentials */
    private $basicAuthCredentials;
    /** @var CustomMatcherDefinition */
    private $customMatcher;
    /** @var ValueMatchingStrategy */
    private $host;

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
