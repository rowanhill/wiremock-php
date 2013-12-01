<?php

namespace WireMock\Client;

use WireMock\Http\ResponseDefinition;

class ResponseDefinitionBuilder
{
    private $_status;
    private $_body;
    private $_bodyFile;
    private $_headers = array();

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
     * @param string $body
     * @return ResponseDefinitionBuilder
     */
    public function withBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * @param $bodyFile
     * @return ResponseDefinitionBuilder
     */
    public function withBodyFile($bodyFile)
    {
        $this->_bodyFile = $bodyFile;
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

    //TODO: withBody (binary)
    //TODO: withFixedDelay
    //TODO: proxiedFrom
    //TODO: withFault

    public function build()
    {
        $responseDefinition = new ResponseDefinition();
        if ($this->_status) {
            $responseDefinition->setStatus($this->_status);
        }
        if ($this->_body) {
            $responseDefinition->setBody($this->_body);
        }
        if ($this->_bodyFile) {
            $responseDefinition->setBodyFile($this->_bodyFile);
        }
        if (!empty($this->_headers)) {
            $responseDefinition->setHeaders($this->_headers);
        }
        return $responseDefinition;
    }
}