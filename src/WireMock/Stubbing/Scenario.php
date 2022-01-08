<?php

namespace WireMock\Stubbing;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class Scenario implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
    const STARTED = 'Started';

    /** @var string UUID */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $state;
    /** @var string[] */
    private $possibleStates;

    /**
     * @param string $id
     * @param string $name
     * @param string $state
     * @param string[] $possibleStates
     */
    public function __construct($id, $name, $state, array $possibleStates)
    {
        $this->id = $id;
        $this->name = $name;
        $this->state = $state;
        $this->possibleStates = $possibleStates;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string[]
     */
    public function getPossibleStates()
    {
        return $this->possibleStates;
    }
}
