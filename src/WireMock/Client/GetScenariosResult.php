<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Stubbing\Scenario;

class GetScenariosResult implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
    /** @var Scenario[] */
    private $scenarios;

    /**
     * @param Scenario[] $scenarios
     */
    public function __construct(array $scenarios)
    {
        $this->scenarios = $scenarios;
    }

    /**
     * @return Scenario[]
     */
    public function getScenarios()
    {
        return $this->scenarios;
    }
}