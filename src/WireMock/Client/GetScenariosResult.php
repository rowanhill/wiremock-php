<?php

namespace WireMock\Client;

use WireMock\Stubbing\Scenario;

class GetScenariosResult
{
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