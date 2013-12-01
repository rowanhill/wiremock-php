<?php

namespace WireMock\Client;

use WireMock\Http\ResponseDefinition;

class ResponseDefinitionBuilder
{
    private $_body;
    private $_bodyFile;

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

    public function build()
    {
        $responseDefinition = new ResponseDefinition();
        if ($this->_body) {
            $responseDefinition->setBody($this->_body);
        }
        if ($this->_bodyFile) {
            $responseDefinition->setBodyFile($this->_bodyFile);
        }
        return $responseDefinition;
    }
}