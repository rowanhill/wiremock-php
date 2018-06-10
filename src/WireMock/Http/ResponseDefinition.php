<?php

namespace WireMock\Http;

use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;
use WireMock\Fault\DelayDistributionFactory;

class ResponseDefinition
{
    /** @var int */
    private $_status = 200;
    /** @var string */
    private $_statusMessage;
    /** @var string */
    private $_body;
    /** @var string */
    private $_bodyFile;
    /** @var string */
    private $_base64Body;
    /** @var array */
    private $_headers;
    /** @var string */
    private $_proxyBaseUrl;
    /** @var array */
    private $_additionalProxyRequestHeaders;
    /** @var int */
    private $_fixedDelayMillis;
    /** @var DelayDistribution */
    protected $_randomDelayDistribution;
    /** @var ChunkedDribbleDelay */
    protected $_chunkedDribbleDelay;
    /** @var string */
    private $_fault;
    /** @var string[] */
    private $_transformers = array();
    /** @var array */
    private $_transformerParameters = array();

    /**
     * ResponseDefinition constructor.
     * @param int $status
     * @param string $statusMessage
     * @param string $body
     * @param string $bodyFile
     * @param string $base64Body
     * @param array $headers
     * @param string $proxyBaseUrl
     * @param array $additionalProxyRequestHeaders
     * @param int $fixedDelayMillis
     * @param DelayDistribution $randomDelayDistribution
     * @param ChunkedDribbleDelay $chunkedDribbleDelay
     * @param string $fault
     * @param string[] $transformers
     * @param array $transformerParameters
     */
    public function __construct(
        $status,
        $statusMessage,
        $body,
        $bodyFile,
        $base64Body,
        $headers,
        $proxyBaseUrl,
        $additionalProxyRequestHeaders,
        $fixedDelayMillis,
        $randomDelayDistribution,
        $chunkedDribbleDelay,
        $fault,
        $transformers,
        $transformerParameters
    ) {
        $this->_status = $status;
        $this->_statusMessage = $statusMessage;
        $this->_body = $body;
        $this->_bodyFile = $bodyFile;
        $this->_base64Body = $base64Body;
        $this->_headers = $headers;
        $this->_proxyBaseUrl = $proxyBaseUrl;
        $this->_fixedDelayMillis = $fixedDelayMillis;
        $this->_randomDelayDistribution = $randomDelayDistribution;
        $this->_chunkedDribbleDelay = $chunkedDribbleDelay;
        $this->_fault = $fault;
        $this->_transformers = $transformers;
        $this->_transformerParameters = $transformerParameters;
        $this->_additionalProxyRequestHeaders = $additionalProxyRequestHeaders;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->_statusMessage;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @return string
     */
    public function getBodyFile()
    {
        return $this->_bodyFile;
    }

    /**
     * @return string
     */
    public function getBase64Body()
    {
        return $this->_base64Body;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return string
     */
    public function getProxyBaseUrl()
    {
        return $this->_proxyBaseUrl;
    }

    /**
     * @return array
     */
    public function getAdditionalProxyRequestHeaders()
    {
        return $this->_additionalProxyRequestHeaders;
    }

    /**
     * @return int
     */
    public function getFixedDelayMillis()
    {
        return $this->_fixedDelayMillis;
    }

    /**
     * @return DelayDistribution
     */
    public function getRandomDelayDistribution()
    {
        return $this->_randomDelayDistribution;
    }

    /**
     * @return ChunkedDribbleDelay
     */
    public function getChunkedDribbleDelay()
    {
        return $this->_chunkedDribbleDelay;
    }

    /**
     * @return string
     */
    public function getFault()
    {
        return $this->_fault;
    }

    /**
     * @return string[]
     */
    public function getTransformers()
    {
        return $this->_transformers;
    }

    /**
     * @return array
     */
    public function getTransformerParameters()
    {
        return $this->_transformerParameters;
    }

    public function toArray()
    {
        $array = array();
        $array['status'] = $this->_status;
        if ($this->_statusMessage) {
            $array['statusMessage'] = $this->_statusMessage;
        }
        if ($this->_body) {
            $array['body'] = $this->_body;
        }
        if ($this->_bodyFile) {
            $array['bodyFileName'] = $this->_bodyFile;
        }
        if ($this->_base64Body) {
            $array['base64Body'] = $this->_base64Body;
        }
        if ($this->_headers) {
            $array['headers'] = $this->_headers;
        }
        if ($this->_proxyBaseUrl) {
            $array['proxyBaseUrl'] = $this->_proxyBaseUrl;
        }
        if ($this->_additionalProxyRequestHeaders) {
            $array['additionalProxyRequestHeaders'] = $this->_additionalProxyRequestHeaders;
        }
        if ($this->_fixedDelayMillis) {
            $array['fixedDelayMilliseconds'] = $this->_fixedDelayMillis;
        }
        if ($this->_randomDelayDistribution) {
            $array['delayDistribution'] = $this->_randomDelayDistribution->toArray();
        }
        if ($this->_chunkedDribbleDelay) {
            $array['chunkedDribbleDelay'] = $this->_chunkedDribbleDelay->toArray();
        }
        if ($this->_fault) {
            $array['fault'] = $this->_fault;
        }
        if ($this->_transformers) {
            $array['transformers'] = $this->_transformers;
        }
        if ($this->_transformerParameters) {
            $array['transformerParameters'] = $this->_transformerParameters;
        }
        return $array;
    }

    /**
     * @param array $array
     * @return ResponseDefinition
     * @throws \Exception
     */
    public static function fromArray(array $array)
    {
        return new ResponseDefinition(
            $array['status'],
            isset($array['statusMessage']) ? $array['statusMessage'] : null,
            isset($array['body']) ? $array['body'] : null,
            isset($array['bodyFileName']) ? $array['bodyFileName'] : null,
            isset($array['base64Body']) ? $array['base64Body'] : null,
            isset($array['headers']) ? $array['headers'] : null,
            isset($array['proxyBaseUrl']) ? $array['proxyBaseUrl'] : null,
            isset($array['additionalProxyRequestHeaders']) ?
                $array['additionalProxyRequestHeaders'] :
                null,
            isset($array['fixedDelayMilliseconds']) ? $array['fixedDelayMilliseconds'] : null,
            isset($array['delayDistribution']) ?
                DelayDistributionFactory::fromArray($array['delayDistribution']) :
                null,
            isset($array['chunkedDribbleDelay']) ?
                ChunkedDribbleDelay::fromArray($array['chunkedDribbleDelay']) :
                null,
            isset($array['fault']) ? $array['fault'] : null,
            isset($array['transformers']) ? $array['transformers'] : null,
            isset($array['transformerParameters']) ? $array['transformerParameters'] : null
        );
    }
}
