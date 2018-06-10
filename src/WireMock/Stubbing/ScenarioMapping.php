<?php

namespace WireMock\Stubbing;

class ScenarioMapping
{
    /** @var string */
    private $_scenarioName;
    /** @var string */
    private $_requiredScenarioState;
    /** @var string */
    private $_newScenarioState;

    /**
     * @param string $scenarioName
     * @param string $requiredScenarioState
     * @param string $newScenarioState
     */
    public function __construct(
        $scenarioName = null,
        $requiredScenarioState = null,
        $newScenarioState = null
    )
    {
        $this->_scenarioName = $scenarioName;
        $this->_requiredScenarioState = $requiredScenarioState;
        $this->_newScenarioState = $newScenarioState;
    }

    /**
     * @return string
     */
    public function getScenarioName()
    {
        return $this->_scenarioName;
    }

    /**
     * @return string
     */
    public function getRequiredScenarioState()
    {
        return $this->_requiredScenarioState;
    }

    /**
     * @return string
     */
    public function getNewScenarioState()
    {
        return $this->_newScenarioState;
    }
}