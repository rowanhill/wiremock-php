<?php

namespace WireMock\Client;

use WireMock\Matching\RequestPattern;
use WireMock\Stubbing\StubMapping;

class MappingBuilder
{
    /** @var RequestPattern */
    private $_requestPattern;
    /** @var ResponseDefinitionBuilder */
    private $_responseDefinitionBuilder;
    private $_headers = array();
    /** @var ValueMatchingStrategy */
    private $_requestBodyPatterns = array();

    public function __construct(RequestPattern $requestPattern)
    {
        $this->_requestPattern = $requestPattern;
    }

    /**
     * @param ResponseDefinitionBuilder $responseDefinitionBuilder
     * @return MappingBuilder
     */
    public function willReturn(ResponseDefinitionBuilder $responseDefinitionBuilder)
    {
        $this->_responseDefinitionBuilder = $responseDefinitionBuilder;
        return $this;
    }

    //TODO: atPriority

    /**
     * @param $headerName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MappingBuilder
     */
    public function withHeader($headerName, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_headers[$headerName] = $valueMatchingStrategy->toArray();
        return $this;
    }

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MappingBuilder
     */
    public function withRequestBody(ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_requestBodyPatterns[] = $valueMatchingStrategy->toArray();
        return $this;
    }

    //TODO: inScenario, whenScenarioStateIs, willSetStateTo

    public function build()
    {
        $responseDefinition = $this->_responseDefinitionBuilder->build();
        $this->_requestPattern->setHeaders($this->_headers);
        if (!empty($this->_requestBodyPatterns)) {
            $this->_requestPattern->setBodyPatterns($this->_requestBodyPatterns);
        }
        return new StubMapping($this->_requestPattern, $responseDefinition);
    }
}