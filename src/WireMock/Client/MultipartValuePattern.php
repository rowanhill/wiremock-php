<?php

namespace WireMock\Client;

class MultipartValuePattern
{
    const ALL = 'ALL';
    const ANY = 'ANY';

    /** @var array */
    private $_bodyPatterns = array();
    /** @var array */
    private $_headers;
    /** @var string */
    private $_name;
    /** @var string */
    private $_matchingType;

    /**
     * @param array $bodyPatterns
     * @param array $headers
     * @param string $name
     * @param string $matchingType
     */
    public function __construct($bodyPatterns, $headers, $name, $matchingType)
    {
        $this->_bodyPatterns = $bodyPatterns;
        $this->_headers = $headers;
        $this->_name = $name;
        $this->_matchingType = $matchingType;
    }

    /**
     * @return array
     */
    public function getBodyPatterns()
    {
        return $this->_bodyPatterns;
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
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getMatchingType()
    {
        return $this->_matchingType;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = array();
        if (!empty($this->_bodyPatterns)) {
            $array['bodyPatterns'] = $this->_bodyPatterns;
        }
        if (!empty($this->_headers)) {
            $array['headers'] = $this->_headers;
        }
        if ($this->_matchingType) {
            $array['matchingType'] = $this->_matchingType;
        }
        return $array;
    }
}