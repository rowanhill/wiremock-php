<?php

namespace WireMock\PostServe;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class PostServeAction implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
    /** @var string */
    private $_name;
    /** @var WebhookDefinition */
    private $_parameters;

    public function __construct(string $name, WebhookDefinition $parameters)
    {
        $this->_name = $name;
        $this->_parameters = $parameters;
    }
}