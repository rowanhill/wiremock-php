<?php

namespace WireMock\Stubbing;

use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;

class StubMapping
{
    /** @var string A string representation of a GUID */
    private $_id;
    /** @var string */
    private $_name;
    /** @var RequestPattern */
    private $_request;
    /** @var ResponseDefinition */
    private $_response;
    /** @var int */
    private $_priority;
    /** @var array */
    private $_metadata;
    /** @var boolean */
    private $_isPersistent;

    /** @var string */
    private $_scenarioName;
    /** @var string */
    private $_requiredScenarioState;
    /** @var string */
    private $_newScenarioState;
    /**
     * @var null
     */
    private $name;

    /**
     * @param RequestPattern $requestPattern
     * @param ResponseDefinition $responseDefinition
     * @param string $id
     * @param string $name
     * @param int $priority
     * @param ScenarioMapping|null $scenarioMapping
     * @param array $metadata
     * @param boolean $isPersistent
     */
    public function __construct(
        RequestPattern $requestPattern,
        ResponseDefinition $responseDefinition,
        $id = null,
        $name = null,
        $priority = null,
        $scenarioMapping = null,
        $metadata = null,
        $isPersistent = null
    )
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_request = $requestPattern;
        $this->_response = $responseDefinition;
        $this->_priority = $priority;
        $this->_metadata = $metadata;
        $this->_isPersistent = $isPersistent;

        if ($scenarioMapping) {
            $this->_scenarioName = $scenarioMapping->getScenarioName();
            $this->_requiredScenarioState = $scenarioMapping->getRequiredScenarioState();
            $this->_newScenarioState = $scenarioMapping->getNewScenarioState();
        }
        $this->name = $name;
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
     * @return string|null
     */
    public function getName()
    {
        return $this->_name;
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
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * @return boolean|null
     */
    public function isPersistent()
    {
        return $this->_isPersistent;
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
        if ($this->_name) {
            $array['name'] = $this->_name;
        }
        if ($this->_priority) {
            $array['priority'] = $this->_priority;
        }
        if ($this->_metadata) {
            $array['metadata'] = $this->_metadata;
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
        if ($this->_isPersistent) {
            $array['persistent'] = $this->_isPersistent;
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
            isset($array['name']) ? $array['name'] : null,
            isset($array['priority']) ? $array['priority'] : null,
            new ScenarioMapping(
                isset($array['scenarioName']) ? $array['scenarioName'] : null,
                isset($array['requiredScenarioState']) ? $array['requiredScenarioState'] : null,
                isset($array['newScenarioState']) ? $array['newScenarioState'] : null
            ),
            isset($array['metadata']) ? $array['metadata'] : null,
            isset($array['persistent']) ? $array['persistent'] : null
        );
    }
}
