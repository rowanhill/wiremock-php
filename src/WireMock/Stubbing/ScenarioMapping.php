<?php

namespace WireMock\Stubbing;

class ScenarioMapping
{
    /** @var string|null */
    private $scenarioName;
    /** @var string|null */
    private $requiredScenarioState;
    /** @var string|null */
    private $newScenarioState;

    /**
     * @param string|null $scenarioName
     * @param string|null $requiredScenarioState
     * @param string|null $newScenarioState
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
     * @return string|null
     */
    public function getScenarioName()
    {
        return $this->scenarioName;
    }

    /**
     * @return string|null
     */
    public function getRequiredScenarioState()
    {
        return $this->requiredScenarioState;
    }

    /**
     * @return string|null
     */
    public function getNewScenarioState()
    {
        return $this->newScenarioState;
    }
}