<?php

namespace WireMock\Http;

class ResponseDefinition
{
    /** @var int */
    private $_status = 200;
    /** @var string */
    private $_body;
    /** @var string */
    private $_bodyFile;
    /** @var array */
    private $_headers;

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->_body = $body;
    }

    public function setBodyFile($bodyFile)
    {
        $this->_bodyFile = $bodyFile;
    }

    public function setHeaders($headers)
    {
        $this->_headers = $headers;
    }

    public function toArray()
    {
        $array = array();
        $array['status'] = $this->_status;
        if ($this->_body) {
            $array['body'] = $this->_body;
        }
        if ($this->_bodyFile) {
            $array['bodyFileName'] = $this->_bodyFile;
        }
        if ($this->_headers) {
            $array['headers'] = $this->_headers;
        }
        return $array;
    }
}