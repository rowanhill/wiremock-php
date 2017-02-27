<?php

namespace WireMock\Stubbing;

class Scenario
{
    const STARTED = 'Started';

    /** @var string */
    private $_scenarioName;
    /** @var string */
    private $_requiredScenarioState;
    /** @var string */
    private $_newScenarioState;

    public function __construct($scenarioName, $requiredScenarioState, $newScenarioState)
    {
        $this->_scenarioName = $scenarioName;
        $this->_requiredScenarioState = $requiredScenarioState;
        $this->_newScenarioState = $newScenarioState;
    }

    public function toArray()
    {
        $array = array('scenarioName' => $this->_scenarioName);
        if ($this->_requiredScenarioState) {
            $array['requiredScenarioState'] = $this->_requiredScenarioState;
        }
        if ($this->_newScenarioState) {
            $array['newScenarioState'] = $this->_newScenarioState;
        }
        return $array;
    }
}
