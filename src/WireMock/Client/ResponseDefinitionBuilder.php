<?php

namespace WireMock\Client;

use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;
use WireMock\Fault\LogNormal;
use WireMock\Fault\UniformDistribution;
use WireMock\Http\ResponseDefinition;

class ResponseDefinitionBuilder
{
    protected $status = 200;
    protected $statusMessage;
    protected $body;
    protected $bodyFile;
    protected $bodyData;
    protected $headers = array();
    protected $proxyBaseUrl;
    protected $fixedDelayMillis;
    /** @var DelayDistribution */
    protected $randomDelayDistribution;
    /** @var ChunkedDribbleDelay */
    protected $chunkedDribbleDelay;
    protected $fault;
    /** @var string[] */
    private $transformers = array();
    /** @var array */
    private $transformerParameters = array();

    protected $additionalRequestHeaders = array();
    /** @var string */
    protected $proxyUrlPrefixToRemove;

    /**
     * @param int $status
     * @return ResponseDefinitionBuilder
     */
    public function withStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $statusMessage
     * @return ResponseDefinitionBuilder
     */
    public function withStatusMessage($statusMessage)
    {
        $this->statusMessage = $statusMessage;
        return $this;
    }

    /**
     * @param string $body
     * @return ResponseDefinitionBuilder
     */
    public function withBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param string $bodyFile
     * @return ResponseDefinitionBuilder
     */
    public function withBodyFile($bodyFile)
    {
        $this->bodyFile = $bodyFile;
        return $this;
    }

    /**
     * @param string $bytesAsString
     * @return ResponseDefinitionBuilder
     */
    public function withBodyData($bytesAsString)
    {
        $base64 = base64_encode($bytesAsString);
        $this->bodyData = $base64;
        return $this;
    }

    /**
     * @param $headerName
     * @param $headerValue
     * @return ResponseDefinitionBuilder
     */
    public function withHeader($headerName, $headerValue)
    {
        if (isset($this->headers[$headerName])) {
            if (is_array($this->headers[$headerName])) {
                $this->headers[$headerName][] = $headerValue;
            } else {
                $this->headers[$headerName] = array($this->headers[$headerName], $headerValue);
            }
        } else {
            $this->headers[$headerName] = $headerValue;
        }
        return $this;
    }

    /**
     * @param string $proxyBaseUrl
     * @return ProxiedResponseDefinitionBuilder
     */
    public function proxiedFrom($proxyBaseUrl)
    {
        $this->proxyBaseUrl = $proxyBaseUrl;
        return new ProxiedResponseDefinitionBuilder($this);
    }

    /**
     * @param int $delayMillis
     * @return ResponseDefinitionBuilder
     */
    public function withFixedDelay($delayMillis)
    {
        $this->fixedDelayMillis = $delayMillis;
        return $this;
    }

    /**
     * @param DelayDistribution $delayDistribution
     * @return ResponseDefinitionBuilder
     */
    public function withRandomDelay($delayDistribution)
    {
        $this->randomDelayDistribution = $delayDistribution;
        return $this;
    }

    /**
     * @param float $median
     * @param float $sigma
     * @return ResponseDefinitionBuilder
     */
    public function withLogNormalRandomDelay($median, $sigma)
    {
        $this->randomDelayDistribution = new LogNormal($median, $sigma);
        return $this;
    }

    /**
     * @param int $lower
     * @param int upper
     * @return ResponseDefinitionBuilder
     */
    public function withUniformRandomDelay($lower, $upper)
    {
        $this->randomDelayDistribution = new UniformDistribution($lower, $upper);
        return $this;
    }

    /**
     * @param int $numberOfChunks
     * @param int $totalDurationMillis
     * @return ResponseDefinitionBuilder
     */
    public function withChunkedDribbleDelay($numberOfChunks, $totalDurationMillis)
    {
        $this->chunkedDribbleDelay = new ChunkedDribbleDelay($numberOfChunks, $totalDurationMillis);
        return $this;
    }

    /**
     * @param $fault
     * @return ResponseDefinitionBuilder
     */
    public function withFault($fault)
    {
        $this->fault = $fault;
        return $this;
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public function withTransformers()
    {
        $this->transformers = func_get_args();
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value Can be any scalar value or array (of scalars/arrays, etc), but must not be an object
     * @return ResponseDefinitionBuilder
     */
    public function withTransformerParameter($name, $value)
    {
        $this->transformerParameters[$name] = $value;
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
            $this->status,
            $this->statusMessage,
            $this->body,
            $this->bodyFile,
            $this->bodyData,
            $this->headers,
            $this->proxyBaseUrl,
            $this->additionalRequestHeaders,
            $this->fixedDelayMillis,
            $this->randomDelayDistribution,
            $this->chunkedDribbleDelay,
            $this->fault,
            $this->transformers,
            $this->transformerParameters,
            $this->proxyUrlPrefixToRemove
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
        $this->additionalRequestHeaders[$headerName] = $value;
        return $this;
    }

    /**
     * @param string $proxyUrlPrefixToRemove
     * @return $this
     */
    public function withProxyUrlPrefixToRemove($proxyUrlPrefixToRemove)
    {
        $this->proxyUrlPrefixToRemove = $proxyUrlPrefixToRemove;
        return $this;
    }
}
