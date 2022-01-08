<?php

namespace WireMock\Client;

use WireMock\PostServe\PostServeAction;
use WireMock\PostServe\WebhookDefinition;
use WireMock\Stubbing\StubMapping;

class MappingBuilder
{
    /** @var string A string representation of a GUID  */
    private $id;
    /** @var string */
    private $name;
    /** @var RequestPatternBuilder */
    private $requestPatternBuilder;
    /** @var ResponseDefinitionBuilder */
    private $responseDefinitionBuilder;
    /** @var int */
    private $priority;
    /** @var ScenarioMappingBuilder */
    private $scenarioBuilder;
    /** @var array */
    private $metadata;
    /** @var boolean */
    private $isPersistent;
    /** @var PostServeAction[]|null */
    private $postServeActions;

    public function __construct(RequestPatternBuilder $requestPatternBuilder)
    {
        $this->requestPatternBuilder = $requestPatternBuilder;
        $this->scenarioBuilder = new ScenarioMappingBuilder();
    }

    /**
     * @param string $id A string representation of a GUID
     * @return MappingBuilder
     */
    public function withId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return MappingBuilder
     */
    public function withName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param ResponseDefinitionBuilder $responseDefinitionBuilder
     * @return MappingBuilder
     */
    public function willReturn(ResponseDefinitionBuilder $responseDefinitionBuilder)
    {
        $this->responseDefinitionBuilder = $responseDefinitionBuilder;
        return $this;
    }

    /**
     * @param int $priority
     * @return MappingBuilder
     */
    public function atPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @param string $headerName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MappingBuilder
     */
    public function withHeader($headerName, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->requestPatternBuilder->withHeader($headerName, $valueMatchingStrategy);
        return $this;
    }

    /**
     * @param string $name
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return $this
     */
    public function withQueryParam($name, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->requestPatternBuilder->withQueryParam($name, $valueMatchingStrategy);
        return $this;
    }

    /**
     * @param string $cookieName
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MappingBuilder
     */
    public function withCookie($cookieName, ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->requestPatternBuilder->withCookie($cookieName, $valueMatchingStrategy);
        return $this;
    }

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return MappingBuilder
     */
    public function withRequestBody(ValueMatchingStrategy $valueMatchingStrategy)
    {
        $this->requestPatternBuilder->withRequestBody($valueMatchingStrategy);
        return $this;
    }

    /**
     * @param MultipartValuePatternBuilder $multipartBuilder
     * @return MappingBuilder
     */
    public function withMultipartRequestBody($multipartBuilder)
    {
        $this->requestPatternBuilder->withMultipartRequestBody($multipartBuilder->build());
        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     * @return MappingBuilder
     */
    public function withBasicAuth($username, $password)
    {
        $this->requestPatternBuilder->withBasicAuth($username, $password);
        return $this;
    }

    /**
     * @param ValueMatchingStrategy $hostMatcher
     * @return $this
     */
    public function withHost($hostMatcher)
    {
        $this->requestPatternBuilder->withHost($hostMatcher);
        return $this;
    }

    /**
     * @param string $scenarioName
     * @return MappingBuilder
     */
    public function inScenario($scenarioName)
    {
        $this->scenarioBuilder->withScenarioName($scenarioName);
        return $this;
    }

    /**
     * @param string $requiredScenarioState
     * @return MappingBuilder
     */
    public function whenScenarioStateIs($requiredScenarioState)
    {
        $this->scenarioBuilder->withRequiredState($requiredScenarioState);
        return $this;
    }

    /**
     * @param string $newScenarioState
     * @return MappingBuilder
     */
    public function willSetStateTo($newScenarioState)
    {
        $this->scenarioBuilder->withNewScenarioState($newScenarioState);
        return $this;
    }

    /**
     * @param array $metadata
     * @return MappingBuilder
     */
    public function withMetadata(array $metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @param string $matcherName
     * @param array $params
     * @return MappingBuilder
     */
    public function andMatching($matcherName, $params = array())
    {
        $this->requestPatternBuilder->withCustomMatcher($matcherName, $params);
        return $this;
    }

    /**
     * @return MappingBuilder
     */
    public function persistent()
    {
        $this->isPersistent = true;
        return $this;
    }

    /**
     * @param string $name Name of the post-serve action
     * @param WebhookDefinition $webhook
     * @return $this
     */
    public function withPostServeAction($name, WebhookDefinition $webhook)
    {
        if (!isset($this->postServeActions)) {
            $this->postServeActions = array();
        }
        $this->postServeActions[] = new PostServeAction($name, $webhook);
        return $this;
    }

    /**
     * @return StubMapping
     * @throws \Exception
     */
    public function build()
    {
        $responseDefinition = $this->responseDefinitionBuilder->build();
        return new StubMapping(
            $this->requestPatternBuilder->build(),
            $responseDefinition,
            $this->id,
            $this->name,
            $this->priority,
            $this->scenarioBuilder->build(),
            $this->metadata,
            $this->isPersistent,
            $this->postServeActions
        );
    }
}
