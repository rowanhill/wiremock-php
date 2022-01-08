<?php

namespace WireMock\Stubbing;

class ScenarioMapping
{
    /** @var string */
    private $scenarioName;
    /** @var string */
    private $requiredScenarioState;
    /** @var string */
    private $newScenarioState;

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
        $this->scenarioName = $scenarioName;
        $this->requiredScenarioState = $requiredScenarioState;
        $this->newScenarioState = $newScenarioState;
    }

    /**
     * @return string
     */
    public function getScenarioName()
    {
        return $this->scenarioName;
    }

    /**
     * @return string
     */
    public function getRequiredScenarioState()
    {
        return $this->requiredScenarioState;
    }

    /**
     * @return string
     */
    public function getNewScenarioState()
    {
        return $this->newScenarioState;
    }
}