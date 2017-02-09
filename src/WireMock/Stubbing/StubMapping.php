<?php

namespace WireMock\Stubbing;

use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;

class StubMapping
{
    /** @var RequestPattern */
    private $_requestPattern;
    /** @var ResponseDefinition */
    private $_responseDefinition;
    /** @var int */
    private $_priority;
    /** @var Scenario */
    private $_scenario;

    /**
     * @param RequestPattern $requestPattern
     * @param ResponseDefinition $responseDefinition
     * @param int $priority
     * @param Scenario $scenario
     */
    public function __construct(
        RequestPattern $requestPattern,
        ResponseDefinition $responseDefinition,
        $priority=null,
        $scenario=null)
    {
        $this->_requestPattern = $requestPattern;
        $this->_responseDefinition = $responseDefinition;
        $this->_priority = $priority;
        $this->_scenario = $scenario;
    }

    public function toArray()
    {
        $array = array(
            'request' => $this->_requestPattern->toArray(),
            'response' => $this->_responseDefinition->toArray()
        );
        if ($this->_priority) {
            $array['priority'] = $this->_priority;
        }
        if ($this->_scenario !== null) {
            $array = array_merge($array, $this->_scenario->toArray());
        }
        return $array;
    }
}
