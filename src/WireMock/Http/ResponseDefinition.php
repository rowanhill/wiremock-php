<?php

namespace WireMock\Http;

use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;

class ResponseDefinition
{
    /** @var int */
    private $status;
    /** @var string */
    private $statusMessage;
    /** @var string */
    private $body;
    /** @var string */
    private $bodyFileName;
    /** @var string */
    private $base64Body;
    /** @var array|null */
    private $headers;
    /** @var string */
    private $proxyBaseUrl;
    /** @var array|null */
    private $additionalProxyRequestHeaders;
    /**
     * @var int
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
    /** @var string */
    private $fault;
    /** @var string[]|null */
    private $transformers;
    /** @var array|null */
    private $transformerParameters;
    /** @var string */
    private $proxyUrlPrefixToRemove;

    /**
     * ResponseDefinition constructor.
     * @param int $status
     * @param string $statusMessage
     * @param string $body
     * @param string $bodyFile
     * @param string $base64Body
     * @param array|null $headers
     * @param string $proxyBaseUrl
     * @param array $additionalProxyRequestHeaders
     * @param int $fixedDelayMillis
     * @param DelayDistribution $randomDelayDistribution
     * @param ChunkedDribbleDelay $chunkedDribbleDelay
     * @param string $fault
     * @param string[]|null $transformers
     * @param array|null $transformerParameters
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
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getBodyFileName()
    {
        return $this->bodyFileName;
    }

    /**
     * @return string
     */
    public function getBase64Body()
    {
        return $this->base64Body;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getProxyBaseUrl()
    {
        return $this->proxyBaseUrl;
    }

    /**
     * @return array
     */
    public function getAdditionalProxyRequestHeaders()
    {
        return $this->additionalProxyRequestHeaders;
    }

    /**
     * @return int
     */
    public function getFixedDelayMillis()
    {
        return $this->fixedDelayMillis;
    }

    /**
     * @return DelayDistribution
     */
    public function getRandomDelayDistribution()
    {
        return $this->randomDelayDistribution;
    }

    /**
     * @return ChunkedDribbleDelay
     */
    public function getChunkedDribbleDelay()
    {
        return $this->chunkedDribbleDelay;
    }

    /**
     * @return string
     */
    public function getFault()
    {
        return $this->fault;
    }

    /**
     * @return string[]
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * @return array
     */
    public function getTransformerParameters()
    {
        return $this->transformerParameters;
    }

    /**
     * @return string
     */
    public function getProxyUrlPrefixToRemove()
    {
        return $this->proxyUrlPrefixToRemove;
    }
}
