<?php

namespace WireMock\Client;

use WireMock\Stubbing\ScenarioMapping;

class ScenarioMappingBuilder
{
    /** @var string */
    private $_scenarioName;
    /** @var string */
    private $_requiredScenarioState;
    /** @var string */
    private $_newScenarioState;

    /**
     * @param string $scenarioName
     * @return ScenarioMappingBuilder
     */
    public function withScenarioName($scenarioName)
    {
        $this->_scenarioName = $scenarioName;
        return $this;
    }

    /**
     * @param string $requiredScenarioState
     * @return ScenarioMappingBuilder
     */
    public function withRequiredState($requiredScenarioState)
    {
        $this->_requiredScenarioState = $requiredScenarioState;
        return $this;
    }

    /**
     * @param string $newScenarioState
     * @return ScenarioMappingBuilder
     */
    public function withNewScenarioState($newScenarioState)
    {
        $this->_newScenarioState = $newScenarioState;
        return $this;
    }

    /**
     * @return null|ScenarioMapping
     * @throws \Exception
     */
    public function build()
    {
        if ($this->_scenarioName === null) {
            if ($this->_requiredScenarioState !== null || $this->_newScenarioState !== null) {
                throw new \Exception('Scenario name must be set');
            }

            return null;
        }

        return new ScenarioMapping($this->_scenarioName, $this->_requiredScenarioState, $this->_newScenarioState);
    }
}
