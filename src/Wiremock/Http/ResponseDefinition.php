<?php

namespace WireMock\Http;

class ResponseDefinition
{
    /** @var int */
    private $_status = 200;
    private $_body;
    private $_bodyFile;

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
        return $array;
    }
}