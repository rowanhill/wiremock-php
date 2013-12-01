<?php

namespace WireMock\Stubbing;

use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;

class StubMappingTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequestPattern */
    private $_mockRequestPattern;
    /** @var ResponseDefinition */
    private $_mockResponseDefinition;

    function setUp()
    {
        $this->_mockRequestPattern = mock('WireMock\Matching\RequestPattern');
        $this->_mockResponseDefinition = mock('WireMock\Http\ResponseDefinition');
    }

    function testRequestPatternAndResponseDefinitionAreAvailableInArray()
    {
        // given
        $requestArray = array('request');
        $responseArray = array('response');
        when($this->_mockRequestPattern->toArray())->return($requestArray);
        when($this->_mockResponseDefinition->toArray())->return($responseArray);
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition);

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('request', $requestArray));
        assertThat($stubMappingArray, hasEntry('response', $responseArray));
    }

    function testPriorityIsInArrayIfSpecified()
    {
        when($this->_mockRequestPattern->toArray())->return(array());
        when($this->_mockResponseDefinition->toArray())->return(array());
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, 5);

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('priority', 5));
    }
}