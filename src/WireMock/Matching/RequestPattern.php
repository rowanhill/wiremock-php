<?php

namespace WireMock\Matching;

class RequestPattern
{
    private $_method;
    private $_urlMatchingStrategy;
    private $_headers;
    private $_bodyPatterns;
    private $_priority;

    /**
     * @param string $method
     * @param UrlMatchingStrategy $urlMatchingStrategy
     */
    public function __construct($method, $urlMatchingStrategy)
    {
        $this->_method = $method;
        $this->_urlMatchingStrategy = $urlMatchingStrategy;
    }

    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;
    }

    public function setBodyPatterns(array $bodyPatterns)
    {
        $this->_bodyPatterns = $bodyPatterns;
    }

    public function toArray()
    {
        $array = array('method' => $this->_method);
        $array = array_merge($array, $this->_urlMatchingStrategy->toArray());
        if ($this->_headers) {
            $array['headers'] = $this->_headers;
        }
        if ($this->_bodyPatterns) {
            $array['bodyPatterns'] = $this->_bodyPatterns;
        }
        if ($this->_priority) {
            $array['priority'] = $this->_priority;
        }
        return $array;
    }
}
