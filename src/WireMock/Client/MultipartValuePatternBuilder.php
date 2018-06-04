<?php

namespace WireMock\Client;

class MultipartValuePatternBuilder
{
    /** @var array */
    private $_bodyPatterns = array();
    /** @var array */
    private $_headers = array();
    /** @var string */
    private $_name;
    /** @var string */
    private $_matchingType = MultipartValuePattern::ANY;

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MultipartValuePatternBuilder
     */
    public function withMultipartBody($valueMatchingStrategy)
    {
        $this->_bodyPatterns[] = $valueMatchingStrategy->toArray();
        return $this;
    }

    /**
     * @param string $name
     * @return MultipartValuePatternBuilder
     */
    public function withName($name)
    {
        $this->_name = $name;
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
        $this->_headers[$headerName] = $valueMatchingStrategy->toArray();
        return $this;
    }

    /**
     * @param string $type
     * @return MultipartValuePatternBuilder
     */
    public function matchingType($type)
    {
        $this->_matchingType = $type;
        return $this;
    }

    public function build()
    {
        return new MultipartValuePattern(
            $this->_bodyPatterns,
            $this->_headers,
            $this->_name,
            $this->_matchingType
        );
    }
}