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

    function __construct(RequestPattern $requestPattern, ResponseDefinition $responseDefinition)
    {
        $this->_requestPattern = $requestPattern;
        $this->_responseDefinition = $responseDefinition;
    }

    function toArray()
    {
        return array(
            'request' => $this->_requestPattern->toArray(),
            'response' => $this->_responseDefinition->toArray()
        );
    }
}