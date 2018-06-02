<?php

namespace WireMock\Client;

use WireMock\Http\ResponseDefinition;

class ResponseDefinitionBuilder
{
    private $_status = 200;
    private $_statusMessage;
    private $_body;
    private $_bodyFile;
    private $_bodyData;
    private $_headers = array();
    private $_proxyBaseUrl;
    private $_fixedDelayMillis;
    private $_fault;

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
     * @return ResponseDefinitionBuilder
     */
    public function proxiedFrom($proxyBaseUrl)
    {
        $this->_proxyBaseUrl = $proxyBaseUrl;
        return $this;
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
            $this->_fixedDelayMillis,
            $this->_fault
        );
    }
}
