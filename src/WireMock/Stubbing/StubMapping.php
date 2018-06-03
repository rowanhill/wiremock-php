<?php

namespace WireMock\Stubbing;

use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;

class StubMapping
{
    /** @var string A string representation of a GUID */
    private $_id;
    /** @var RequestPattern */
    private $_request;
    /** @var ResponseDefinition */
    private $_response;
    /** @var int */
    private $_priority;

    /** @var string */
    private $_scenarioName;
    /** @var string */
    private $_requiredScenarioState;
    /** @var string */
    private $_newScenarioState;

    /**
     * @param RequestPattern $requestPattern
     * @param ResponseDefinition $responseDefinition
     * @param string $id
     * @param int $priority
     * @param ScenarioMapping|null $scenarioMapping
     */
    public function __construct(
        RequestPattern $requestPattern,
        ResponseDefinition $responseDefinition,
        $id = null,
        $priority = null,
        $scenarioMapping = null
    )
    {
        $this->_id = $id;
        $this->_request = $requestPattern;
        $this->_response = $responseDefinition;
        $this->_priority = $priority;

        if ($scenarioMapping) {
            $this->_scenarioName = $scenarioMapping->getScenarioName();
            $this->_requiredScenarioState = $scenarioMapping->getRequiredScenarioState();
            $this->_newScenarioState = $scenarioMapping->getNewScenarioState();
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return RequestPattern
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return ResponseDefinition
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->_priority;
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

    public function toArray()
    {
        $array = array(
            'request' => $this->_request->toArray(),
            'response' => $this->_response->toArray(),
        );
        if ($this->_id) {
            $array['id'] = $this->_id;
        }
        if ($this->_priority) {
            $array['priority'] = $this->_priority;
        }
        if ($this->_scenarioName) {
            $array['scenarioName'] = $this->_scenarioName;
        }
        if ($this->_requiredScenarioState) {
            $array['requiredScenarioState'] = $this->_requiredScenarioState;
        }
        if ($this->_newScenarioState) {
            $array['newScenarioState'] = $this->_newScenarioState;
        }
        return $array;
    }

    /**
     * @param array $array
     * @return StubMapping
     * @throws \Exception
     */
    public static function fromArray(array $array)
    {
        return new StubMapping(
            RequestPattern::fromArray($array['request']),
            ResponseDefinition::fromArray($array['response']),
            $array['id'],
            isset($array['priority']) ?: null,
            new ScenarioMapping(
                isset($array['scenarioName']) ?: null,
                isset($array['requiredScenarioState']) ?: null,
                isset($array['newScenarioState']) ?: null
            )
        );
    }
}
