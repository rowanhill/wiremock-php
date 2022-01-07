<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Stubbing\Scenario;

class GetScenariosResult implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
    /** @var Scenario[] */
    private $_scenarios;

    /**
     * @param Scenario[] $scenarios
     */
    public function __construct(array $scenarios)
    {
        $this->_scenarios = $scenarios;
    }

    /**
     * @return Scenario[]
     */
    public function getScenarios()
    {
        return $this->_scenarios;
    }
}