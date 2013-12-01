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
        /** @var MappingBuilder $this */
        return $this;
    }

    //TODO: atPriority
    //TODO: withHeader
    //TODO: withRequestBody

    //TODO: inScenario, whenScenarioStateIs, willSetStateTo

    public function build()
    {
        $responseDefinition = $this->_responseDefinitionBuilder->build();
        return new StubMapping($this->_requestPattern, $responseDefinition);
    }
}