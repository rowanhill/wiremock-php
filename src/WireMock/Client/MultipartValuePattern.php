<?php

namespace WireMock\Client;

class MultipartValuePattern
{
    const ALL = 'ALL';
    const ANY = 'ANY';

    /** @var ValueMatchingStrategy[]|null */
    private $bodyPatterns;
    /** @var array<string, ValueMatchingStrategy>|null */
    private $headers;
    /** @var string|null */
    private $name;
    /** @var string|null */
    private $matchingType;

    /**
     * @param ValueMatchingStrategy[]|null $bodyPatterns
     * @param array<string, ValueMatchingStrategy>|null $headers
     * @param string|null $name
     * @param string|null $matchingType
     */
    public function __construct($bodyPatterns = null, $headers = null, $name = null, $matchingType = null)
    {
        $this->bodyPatterns = $bodyPatterns;
        $this->headers = $headers;
        $this->name = $name;
        $this->matchingType = $matchingType;
    }

    /**
     * @return ValueMatchingStrategy[]|null
     */
    public function getBodyPatterns()
    {
        return $this->bodyPatterns;
    }

    /**
     * @return array<string, ValueMatchingStrategy>|null
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getMatchingType()
    {
        return $this->matchingType;
    }
}