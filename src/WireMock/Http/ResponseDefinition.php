<?php

namespace WireMock\Http;

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
    /** @var string */
    private $_fault;

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
     * @param string $fault
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
        $fault
    ) {
        $this->_status = $status;
        $this->_statusMessage = $statusMessage;
        $this->_body = $body;
        $this->_bodyFile = $bodyFile;
        $this->_base64Body = $base64Body;
        $this->_headers = $headers;
        $this->_proxyBaseUrl = $proxyBaseUrl;
        $this->_fixedDelayMillis = $fixedDelayMillis;
        $this->_fault = $fault;
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
     * @return string
     */
    public function getFault()
    {
        return $this->_fault;
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
        if ($this->_fault) {
            $array['fault'] = $this->_fault;
        }
        return $array;
    }

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
            isset($array['fault']) ? $array['fault'] : null
        );
    }
}
