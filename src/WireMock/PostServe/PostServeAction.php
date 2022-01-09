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
}