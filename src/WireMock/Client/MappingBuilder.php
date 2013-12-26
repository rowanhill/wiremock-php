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
    /** @var int */
    private $_priority;
    /** @var ScenarioBuilder */
    private $_scenarioBuilder;

    public function __construct(RequestPattern $requestPattern)
    {
        $this->_requestPattern = $requestPattern;
        $this->_scenarioBuilder = new ScenarioBuilder();
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

    /**
     * @param int $priority
     * @return MappingBuilder
     */
    public function atPriority($priority)
    {
        $this->_priority = $priority;
        return $this;
    }

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

    /**
     * @param string $scenarioName
     * @return MappingBuilder
     */
    public function inScenario($scenarioName)
    {
        $this->_scenarioBuilder->withScenarioName($scenarioName);
        return $this;
    }

    /**
     * @param string $requiredScenarioState
     * @return MappingBuilder
     */
    public function whenScenarioStateIs($requiredScenarioState)
    {
        $this->_scenarioBuilder->withRequiredState($requiredScenarioState);
        return $this;
    }

    /**
     * @param string $newScenarioState
     * @return MappingBuilder
     */
    public function willSetStateTo($newScenarioState)
    {
        $this->_scenarioBuilder->withNewScenarioState($newScenarioState);
        return $this;
    }

    public function build()
    {
        $responseDefinition = $this->_responseDefinitionBuilder->build();
        $this->_requestPattern->setHeaders($this->_headers);
        if (!empty($this->_requestBodyPatterns)) {
            $this->_requestPattern->setBodyPatterns($this->_requestBodyPatterns);
        }
        return new StubMapping(
            $this->_requestPattern,
            $responseDefinition,
            $this->_priority,
            $this->_scenarioBuilder->build());
    }
}