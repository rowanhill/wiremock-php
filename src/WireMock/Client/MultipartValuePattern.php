<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class MultipartValuePattern implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    const ALL = 'ALL';
    const ANY = 'ANY';

    /** @var ValueMatchingStrategy[] */
    private $_bodyPatterns;
    /** @var \array<string, ValueMatchingStrategy> */
    private $_headers;
    /** @var string */
    private $_name;
    /** @var string */
    private $_matchingType;

    /**
     * @param ValueMatchingStrategy[] $bodyPatterns
     * @param array<string, ValueMatchingStrategy> $headers
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
            $array['bodyPatterns'] = array_map(function($bp) { return $bp->toArray(); }, $this->_bodyPatterns);
        }
        if (!empty($this->_headers)) {
            $array['headers'] = array_map(function($h) { return $h->toArray(); }, $this->_headers);
        }
        if ($this->_matchingType) {
            $array['matchingType'] = $this->_matchingType;
        }
        return $array;
    }
}