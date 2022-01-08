<?php

namespace WireMock\Client;

use WireMock\Stubbing\ScenarioMapping;

class ScenarioMappingBuilder
{
    /** @var string */
    private $scenarioName;
    /** @var string */
    private $requiredScenarioState;
    /** @var string */
    private $newScenarioState;

    /**
     * @param string $scenarioName
     * @return ScenarioMappingBuilder
     */
    public function withScenarioName($scenarioName)
    {
        $this->scenarioName = $scenarioName;
        return $this;
    }

    /**
     * @param string $requiredScenarioState
     * @return ScenarioMappingBuilder
     */
    public function withRequiredState($requiredScenarioState)
    {
        $this->requiredScenarioState = $requiredScenarioState;
        return $this;
    }

    /**
     * @param string $newScenarioState
     * @return ScenarioMappingBuilder
     */
    public function withNewScenarioState($newScenarioState)
    {
        $this->newScenarioState = $newScenarioState;
        return $this;
    }

    /**
     * @return null|ScenarioMapping
     * @throws \Exception
     */
    public function build()
    {
        if ($this->scenarioName === null) {
            if ($this->requiredScenarioState !== null || $this->newScenarioState !== null) {
                throw new \Exception('Scenario name must be set');
            }

            return null;
        }

        return new ScenarioMapping($this->scenarioName, $this->requiredScenarioState, $this->newScenarioState);
    }
}
