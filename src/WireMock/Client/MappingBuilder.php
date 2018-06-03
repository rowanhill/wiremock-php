<?php

namespace WireMock\Client;

use WireMock\Stubbing\StubMapping;

class MappingBuilder
{
    /** @var string A string representation of a GUID  */
    private $_id;
    /** @var RequestPatternBuilder */
    private $_requestPatternBuilder;
    /** @var ResponseDefinitionBuilder */
    private $_responseDefinitionBuilder;
    /** @var int */
    private $_priority;
    /** @var ScenarioMappingBuilder */
    private $_scenarioBuilder;

    public function __construct(RequestPatternBuilder $requestPatternBuilder)
    {
        $this->_requestPatternBuilder = $requestPatternBuilder;
        $this->_scenarioBuilder = new ScenarioMappingBuilder();
    }

    /**
     * @param string $id A string representation of a GUID
     * @return MappingBuilder
     */
    public function withId($id)
    {
        $this->_id = $id;
        return $this;
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
     * @param string $headerName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MappingBuilder
     */
    public function withHeader($headerName, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_requestPatternBuilder->withHeader($headerName, $valueMatchingStrategy);
        return $this;
    }

    /**
     * @param string $name
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return $this
     */
    public function withQueryParam($name, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_requestPatternBuilder->withQueryParam($name, $valueMatchingStrategy);
        return $this;
    }

    /**
     * @param string $cookieName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MappingBuilder
     */
    public function withCookie($cookieName, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_requestPatternBuilder->withCookie($cookieName, $valueMatchingStrategy);
        return $this;
    }

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MappingBuilder
     */
    public function withRequestBody(ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->_requestPatternBuilder->withRequestBody($valueMatchingStrategy);
        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     * @return MappingBuilder
     */
    public function withBasicAuth($username, $password)
    {
        $this->_requestPatternBuilder->withBasicAuth($username, $password);
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

    /**
     * @return StubMapping
     * @throws \Exception
     */
    public function build()
    {
        $responseDefinition = $this->_responseDefinitionBuilder->build();
        return new StubMapping(
            $this->_requestPatternBuilder->build(),
            $responseDefinition,
            $this->_id,
            $this->_priority,
            $this->_scenarioBuilder->build());
    }
}
