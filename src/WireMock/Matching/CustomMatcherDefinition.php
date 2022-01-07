<?php

namespace WireMock\Matching;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class CustomMatcherDefinition implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var string */
    private $_name;
    /** @var array */
    private $_parameters;

    /**
     * @param string $name
     * @param array $parameters
     */
    public function __construct($name, array $parameters)
    {
        $this->_name = $name;
        $this->_parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }
}