<?php

namespace WireMock\PostServe;

class PostServeAction
{
    /** @var string */
    private $name;
    /** @var WebhookDefinition */
    private $parameters;

    public function __construct(string $name, WebhookDefinition $parameters)
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return WebhookDefinition
     */
    public function getParameters(): WebhookDefinition
    {
        return $this->parameters;
    }
}