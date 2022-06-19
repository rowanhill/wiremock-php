<?php

namespace WireMock\Client;

class MultipartValuePatternBuilder
{
    /** @var ValueMatchingStrategy[]|null */
    private $bodyPatterns;
    /** @var array<string, ValueMatchingStrategy>|null */
    private $headers;
    /** @var string */
    private $name;
    /** @var string */
    private $matchingType = MultipartValuePattern::ANY;

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MultipartValuePatternBuilder
     */
    public function withMultipartBody($valueMatchingStrategy)
    {
        $this->bodyPatterns[] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param string $name
     * @return MultipartValuePatternBuilder
     */
    public function withName($name)
    {
        $this->name = $name;
        $this->withHeader('Content-Disposition', WireMock::containing("name=\"$name\""));
        return $this;
    }

    /**
     * @param string $headerName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MultipartValuePatternBuilder
     */
    public function withHeader($headerName, $valueMatchingStrategy)
    {
        $this->headers[$headerName] = $valueMatchingStrategy;
        return $this;
    }

    /**
     * @param string $type
     * @return MultipartValuePatternBuilder
     */
    public function matchingType($type)
    {
        $this->matchingType = $type;
        return $this;
    }

    public function build()
    {
        return new MultipartValuePattern(
            $this->bodyPatterns,
            $this->headers,
            $this->name,
            $this->matchingType
        );
    }
}