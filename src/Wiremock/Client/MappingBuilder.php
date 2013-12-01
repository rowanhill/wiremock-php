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
    //TODO: withRequestBody

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

    //TODO: inScenario, whenScenarioStateIs, willSetStateTo

    public function build()
    {
        $responseDefinition = $this->_responseDefinitionBuilder->build();
        $this->_requestPattern->setHeaders($this->_headers);
        return new StubMapping($this->_requestPattern, $responseDefinition);
    }
}