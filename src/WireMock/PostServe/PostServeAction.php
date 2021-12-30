<?php

namespace WireMock\PostServe;

class PostServeAction
{
    /** @var string */
    private $_name;
    /** @var WebhookDefinition */
    private $_parameters;

    public function __construct(string $name, WebhookDefinition $parameters)
    {
        $this->_name = $name;
        $this->_parameters = $parameters;
    }

    public function toArray(): array
    {
        return array(
            'name' => $this->_name,
            'parameters' => $this->_parameters->toArray(),
        );
    }

    public static function fromArray(array $array): self
    {
        return new self($array['name'], WebhookDefinition::fromArray($array['parameters']));
    }
}