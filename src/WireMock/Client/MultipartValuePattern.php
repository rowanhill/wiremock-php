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
    /** @var string */
    private $name;
    /** @var string */
    private $matchingType;

    /**
     * @param ValueMatchingStrategy[]|null $bodyPatterns
     * @param array<string, ValueMatchingStrategy>|null $headers
     * @param string $name
     * @param string $matchingType
     */
    public function __construct($bodyPatterns = null, $headers = null, $name = null, $matchingType = null)
    {
        $this->bodyPatterns = $bodyPatterns;
        $this->headers = $headers;
        $this->name = $name;
        $this->matchingType = $matchingType;
    }

    /**
     * @return array
     */
    public function getBodyPatterns()
    {
        return $this->bodyPatterns;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMatchingType()
    {
        return $this->matchingType;
    }
}