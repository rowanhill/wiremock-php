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

    /**
     * @param RequestPattern $requestPattern
     * @param ResponseDefinition $responseDefinition
     * @param int $priority
     */
    function __construct(RequestPattern $requestPattern, ResponseDefinition $responseDefinition, $priority=null)
    {
        $this->_requestPattern = $requestPattern;
        $this->_responseDefinition = $responseDefinition;
        $this->_priority = $priority;
    }

    function toArray()
    {
        $array = array(
            'request' => $this->_requestPattern->toArray(),
            'response' => $this->_responseDefinition->toArray()
        );
        if ($this->_priority) {
            $array['priority'] = $this->_priority;
        }
        return $array;
    }
}