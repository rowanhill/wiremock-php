<?php

namespace WireMock\Http;

use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;

class ResponseDefinition
{
    /** @var int */
    private $status;
    /** @var string|null */
    private $statusMessage;
    /** @var string|null */
    private $body;
    /** @var string|null */
    private $bodyFileName;
    /** @var string|null */
    private $base64Body;
    /** @var array<string, string|int>|null */
    private $headers;
    /** @var string|null */
    private $proxyBaseUrl;
    /** @var array<string, string|int>|null */
    private $additionalProxyRequestHeaders;
    /**
     * @var int|null
     * @serde-name fixedDelayMilliseconds
     */
    private $fixedDelayMillis;
    /**
     * @var ?DelayDistribution
     * @serde-name delayDistribution
     */
    protected $randomDelayDistribution;
    /** @var ?ChunkedDribbleDelay */
    protected $chunkedDribbleDelay;
    /** @var string|null */
    private $fault;
    /** @var string[]|null */
    private $transformers;
    /** @var array<string, string|int>|null */
    private $transformerParameters;
    /** @var string|null */
    private $proxyUrlPrefixToRemove;

    /**
     * ResponseDefinition constructor.
     * @param int $status
     * @param string|null $statusMessage
     * @param string|null $body
     * @param string|null $bodyFile
     * @param string|null $base64Body
     * @param array<int|string, mixed>|null $headers
     * @param string|null $proxyBaseUrl
     * @param array<int|string, mixed>|null $additionalProxyRequestHeaders
     * @param int|null $fixedDelayMillis
     * @param DelayDistribution|null $randomDelayDistribution
     * @param ChunkedDribbleDelay|null $chunkedDribbleDelay
     * @param string|null $fault
     * @param string[]|null $transformers
     * @param array<int|string, mixed>|null $transformerParameters
     * @param string|null $proxyUrlPrefixToRemove
     */
    public function __construct(
        $status,
        $statusMessage = null,
        $body = null,
        $bodyFile = null,
        $base64Body = null,
        $headers = null,
        $proxyBaseUrl = null,
        $additionalProxyRequestHeaders = null,
        $fixedDelayMillis = null,
        $randomDelayDistribution = null,
        $chunkedDribbleDelay = null,
        $fault = null,
        $transformers = null,
        $transformerParameters = null,
        $proxyUrlPrefixToRemove = null
    ) {
        $this->status = $status;
        $this->statusMessage = $statusMessage;
        $this->body = $body;
        $this->bodyFileName = $bodyFile;
        $this->base64Body = $base64Body;
        $this->headers = $headers;
        $this->proxyBaseUrl = $proxyBaseUrl;
        $this->fixedDelayMillis = $fixedDelayMillis;
        $this->randomDelayDistribution = $randomDelayDistribution;
        $this->chunkedDribbleDelay = $chunkedDribbleDelay;
        $this->fault = $fault;
        $this->transformers = $transformers;
        $this->transformerParameters = $transformerParameters;
        $this->additionalProxyRequestHeaders = $additionalProxyRequestHeaders;
        $this->proxyUrlPrefixToRemove = $proxyUrlPrefixToRemove;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @return string|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string|null
     */
    public function getBodyFileName()
    {
        return $this->bodyFileName;
    }

    /**
     * @return string|null
     */
    public function getBase64Body()
    {
        return $this->base64Body;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string|null
     */
    public function getProxyBaseUrl()
    {
        return $this->proxyBaseUrl;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getAdditionalProxyRequestHeaders()
    {
        return $this->additionalProxyRequestHeaders;
    }

    /**
     * @return int|null
     */
    public function getFixedDelayMillis()
    {
        return $this->fixedDelayMillis;
    }

    /**
     * @return DelayDistribution|null
     */
    public function getRandomDelayDistribution()
    {
        return $this->randomDelayDistribution;
    }

    /**
     * @return ChunkedDribbleDelay|null
     */
    public function getChunkedDribbleDelay()
    {
        return $this->chunkedDribbleDelay;
    }

    /**
     * @return string|null
     */
    public function getFault()
    {
        return $this->fault;
    }

    /**
     * @return string[]|null
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getTransformerParameters()
    {
        return $this->transformerParameters;
    }

    /**
     * @return string|null
     */
    public function getProxyUrlPrefixToRemove()
    {
        return $this->proxyUrlPrefixToRemove;
    }
}
