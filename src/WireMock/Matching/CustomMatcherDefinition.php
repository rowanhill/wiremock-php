<?php

namespace WireMock\Matching;

class CustomMatcherDefinition
{
    /** @var string */
    private $name;
    /** @var array */
    private $parameters;

    /**
     * @param string $name
     * @param array $parameters
     */
    public function __construct($name, array $parameters)
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}