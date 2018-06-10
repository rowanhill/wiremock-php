<?php

namespace WireMock\Client;

use WireMock\Stubbing\Scenario;

class GetScenariosResult
{
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

    public static function fromArray($array)
    {
        return new GetScenariosResult(
            array_map(function($s) { return Scenario::fromArray($s); }, $array['scenarios'])
        );
    }
}