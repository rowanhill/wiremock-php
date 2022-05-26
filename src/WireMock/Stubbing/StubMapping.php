<?php

namespace WireMock\Stubbing;

use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;
use WireMock\PostServe\PostServeAction;

class StubMapping
{
    /** @var string A string representation of a GUID */
    private $id;
    /** @var string */
    private $name;
    /** @var RequestPattern */
    private $request;
    /** @var ResponseDefinition */
    private $response;
    /** @var int */
    private $priority;
    /** @var array */
    private $metadata;
    /** @var boolean */
    private $persistent;

    /** @var string */
    private $scenarioName;
    /** @var string */
    private $requiredScenarioState;
    /** @var string */
    private $newScenarioState;
    /** @var PostServeAction[]|null */
    private $postServeActions;

    /**
     * @param RequestPattern $request
     * @param ResponseDefinition $response
     * @param string $id
     * @param string $name
     * @param int $priority
     * @param ScenarioMapping|null $scenarioMapping
     * @param array $metadata
     * @param boolean $persistent
     * @param array|null $postServeActions
     */
    public function __construct(
        RequestPattern $request,
        ResponseDefinition $response,
        $id = null,
        $name = null,
        $priority = null,
        $scenarioMapping = null,
        $metadata = null,
        $persistent = null,
        $postServeActions = null
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->request = $request;
        $this->response = $response;
        $this->priority = $priority;
        $this->metadata = $metadata;
        $this->persistent = $persistent;
        $this->postServeActions = $postServeActions;

        if ($scenarioMapping) {
            $this->scenarioName = $scenarioMapping->getScenarioName();
            $this->requiredScenarioState = $scenarioMapping->getRequiredScenarioState();
            $this->newScenarioState = $scenarioMapping->getNewScenarioState();
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return RequestPattern
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseDefinition
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return boolean|null
     */
    public function isPersistent()
    {
        return $this->persistent;
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

    /**
     * @return PostServeAction[]|null
     */
    public function getPostServeActions(): ?array
    {
        return $this->postServeActions;
    }
}
