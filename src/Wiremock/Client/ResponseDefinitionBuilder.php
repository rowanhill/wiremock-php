<?php

namespace WireMock\Client;

use WireMock\Http\ResponseDefinition;

class ResponseDefinitionBuilder
{
    private $_body;
    private $_bodyFile;
    private $_headers = array();

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

    public function build()
    {
        $responseDefinition = new ResponseDefinition();
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