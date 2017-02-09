<?php

namespace WireMock\Client;

use WireMock\Stubbing\Scenario;

class ScenarioBuilder
{
    /** @var string */
    private $_scenarioName = null;
    /** @var string */
    private $_requiredScenarioState = null;
    /** @var string */
    private $_newScenarioState = null;

    /**
     * @param string $scenarioName
     * @return ScenarioBuilder
     */
    public function withScenarioName($scenarioName)
    {
        $this->_scenarioName = $scenarioName;
        return $this;
    }

    /**
     * @param string $requiredScenarioState
     * @return ScenarioBuilder
     */
    public function withRequiredState($requiredScenarioState) {
        $this->_requiredScenarioState = $requiredScenarioState;
        return $this;
    }

    /**
     * @param string $newScenarioState
     * @return ScenarioBuilder
     */
    public function withNewScenarioState($newScenarioState) {
        $this->_newScenarioState = $newScenarioState;
        return $this;
    }

    public function build() {
        if ($this->_scenarioName === null) {
            if ($this->_requiredScenarioState !== null || $this->_newScenarioState !== null) {
                throw new \Exception('Scenario name must be set');
            } else {
                return null;
            }
        } else {
            return new Scenario($this->_scenarioName, $this->_requiredScenarioState, $this->_newScenarioState);
        }
    }
}
