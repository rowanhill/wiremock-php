<?php

namespace WireMock\Stubbing;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class Scenario implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
    const STARTED = 'Started';

    /** @var string UUID */
    private $_id;
    /** @var string */
    private $_name;
    /** @var string */
    private $_state;
    /** @var string[] */
    private $_possibleStates;

    /**
     * @param string $id
     * @param string $name
     * @param string $state
     * @param string[] $possibleStates
     */
    public function __construct($id, $name, $state, array $possibleStates)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_state = $state;
        $this->_possibleStates = $possibleStates;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * @return string[]
     */
    public function getPossibleStates()
    {
        return $this->_possibleStates;
    }
}
