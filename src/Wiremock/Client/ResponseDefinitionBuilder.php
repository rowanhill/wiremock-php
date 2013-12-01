<?php

namespace WireMock\Client;

use WireMock\Http\ResponseDefinition;

class ResponseDefinitionBuilder
{
    private $_body;

    /**
     * @param string $body
     * @return ResponseDefinitionBuilder
     */
    public function withBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    public function build()
    {
        $responseDefinition = new ResponseDefinition();
        if ($this->_body) {
            $responseDefinition->setBody($this->_body);
        }
        return $responseDefinition;
    }
}