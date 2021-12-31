<?php

namespace WireMock\Client;

use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;
use WireMock\Fault\LogNormal;
use WireMock\Fault\UniformDistribution;
use WireMock\Http\ResponseDefinition;

class ResponseDefinitionBuilder
{
    protected $_status = 200;
    protected $_statusMessage;
    protected $_body;
    protected $_bodyFile;
    protected $_bodyData;
    protected $_headers = array();
    protected $_proxyBaseUrl;
    protected $_fixedDelayMillis;
    /** @var DelayDistribution */
    protected $_randomDelayDistribution;
    /** @var ChunkedDribbleDelay */
    protected $_chunkedDribbleDelay;
    protected $_fault;
    /** @var string[] */
    private $_transformers = array();
    /** @var array */
    private $_transformerParameters = array();

    protected $_additionalRequestHeaders = array();
    /** @var string */
    protected $_proxyUrlPrefixToRemove;

    /**
     * @param int $status
     * @return ResponseDefinitionBuilder
     */
    public function withStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    /**
     * @param string $statusMessage
     * @return ResponseDefinitionBuilder
     */
    public function withStatusMessage($statusMessage)
    {
        $this->_statusMessage = $statusMessage;
        return $this;
    }

    /**
     * @param string $body
     * @return ResponseDefinitionBuilder
     */
    public function withBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * @param string $bodyFile
     * @return ResponseDefinitionBuilder
     */
    public function withBodyFile($bodyFile)
    {
        $this->_bodyFile = $bodyFile;
        return $this;
    }

    /**
     * @param string $bytesAsString
     * @return ResponseDefinitionBuilder
     */
    public function withBodyData($bytesAsString)
    {
        $base64 = base64_encode($bytesAsString);
        $this->_bodyData = $base64;
        return $this;
    }

    /**
     * @param $headerName
     * @param $headerValue
     * @return ResponseDefinitionBuilder
     */
    public function withHeader($headerName, $headerValue)
    {
        if (isset($this->_headers[$headerName])) {
            if (is_array($this->_headers[$headerName])) {
                $this->_headers[$headerName][] = $headerValue;
            } else {
                $this->_headers[$headerName] = array($this->_headers[$headerName], $headerValue);
            }
        } else {
            $this->_headers[$headerName] = $headerValue;
        }
        return $this;
    }

    /**
     * @param string $proxyBaseUrl
     * @return ProxiedResponseDefinitionBuilder
     */
    public function proxiedFrom($proxyBaseUrl)
    {
        $this->_proxyBaseUrl = $proxyBaseUrl;
        return new ProxiedResponseDefinitionBuilder($this);
    }

    /**
     * @param int $delayMillis
     * @return ResponseDefinitionBuilder
     */
    public function withFixedDelay($delayMillis)
    {
        $this->_fixedDelayMillis = $delayMillis;
        return $this;
    }

    /**
     * @param DelayDistribution $delayDistribution
     * @return ResponseDefinitionBuilder
     */
    public function withRandomDelay($delayDistribution)
    {
        $this->_randomDelayDistribution = $delayDistribution;
        return $this;
    }

    /**
     * @param float $median
     * @param float $sigma
     * @return ResponseDefinitionBuilder
     */
    public function withLogNormalRandomDelay($median, $sigma)
    {
        $this->_randomDelayDistribution = new LogNormal($median, $sigma);
        return $this;
    }

    /**
     * @param int $lower
     * @param int upper
     * @return ResponseDefinitionBuilder
     */
    public function withUniformRandomDelay($lower, $upper)
    {
        $this->_randomDelayDistribution = new UniformDistribution($lower, $upper);
        return $this;
    }

    /**
     * @param int $numberOfChunks
     * @param int $totalDurationMillis
     * @return ResponseDefinitionBuilder
     */
    public function withChunkedDribbleDelay($numberOfChunks, $totalDurationMillis)
    {
        $this->_chunkedDribbleDelay = new ChunkedDribbleDelay($numberOfChunks, $totalDurationMillis);
        return $this;
    }

    /**
     * @param $fault
     * @return ResponseDefinitionBuilder
     */
    public function withFault($fault)
    {
        $this->_fault = $fault;
        return $this;
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public function withTransformers()
    {
        $this->_transformers = func_get_args();
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value Can be any scalar value or array (of scalars/arrays, etc), but must not be an object
     * @return ResponseDefinitionBuilder
     */
    public function withTransformerParameter($name, $value)
    {
        $this->_transformerParameters[$name] = $value;
        return $this;
    }

    /**
     * @param string $transformerName
     * @param string $paramName
     * @param mixed $paramValue Can be any scalar value or array (of scalars/arrays, etc), but must not be an object
     * @return ResponseDefinitionBuilder
     */
    public function withTransformer($transformerName, $paramName, $paramValue)
    {
        $this->withTransformers($transformerName);
        $this->withTransformerParameter($paramName, $paramValue);
        return $this;
    }

    public function build()
    {
        return new ResponseDefinition(
            $this->_status,
            $this->_statusMessage,
            $this->_body,
            $this->_bodyFile,
            $this->_bodyData,
            $this->_headers,
            $this->_proxyBaseUrl,
            $this->_additionalRequestHeaders,
            $this->_fixedDelayMillis,
            $this->_randomDelayDistribution,
            $this->_chunkedDribbleDelay,
            $this->_fault,
            $this->_transformers,
            $this->_transformerParameters,
            $this->_proxyUrlPrefixToRemove
        );
    }
}

class ProxiedResponseDefinitionBuilder extends ResponseDefinitionBuilder
{
    /**
     * @param ResponseDefinitionBuilder $from
     */
    public function __construct($from)
    {
        $vars = get_object_vars($from);
        foreach ($vars as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @param string $headerName
     * @param string $value
     * @return ProxiedResponseDefinitionBuilder
     */
    public function withAdditionalRequestHeader($headerName, $value)
    {
        $this->_additionalRequestHeaders[$headerName] = $value;
        return $this;
    }

    /**
     * @param string $proxyUrlPrefixToRemove
     * @return $this
     */
    public function withProxyUrlPrefixToRemove($proxyUrlPrefixToRemove)
    {
        $this->_proxyUrlPrefixToRemove = $proxyUrlPrefixToRemove;
        return $this;
    }
}
