<?php

namespace WireMock\PostServe;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class PostServeAction implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
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