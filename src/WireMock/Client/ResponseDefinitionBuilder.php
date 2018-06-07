<?php

namespace WireMock\Client;

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
    protected $_fault;

    protected $_additionalRequestHeaders = array();

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
        $this->_headers[$headerName] = $headerValue;
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
     * @param $fault
     * @return ResponseDefinitionBuilder
     */
    public function withFault($fault)
    {
        $this->_fault = $fault;
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
            $this->_fault
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
}
