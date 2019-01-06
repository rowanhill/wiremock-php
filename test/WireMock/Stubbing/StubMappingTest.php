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

    public function setUp()
    {
        $this->_mockRequestPattern = mock('WireMock\Matching\RequestPattern');
        $this->_mockResponseDefinition = mock('WireMock\Http\ResponseDefinition');
    }

    public function testRequestPatternAndResponseDefinitionAreAvailableInArray()
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

    public function testIdIsInArrayIfSpecified()
    {
        // given
        when($this->_mockRequestPattern->toArray())->return(array());
        when($this->_mockResponseDefinition->toArray())->return(array());
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, 'some-long-guid');

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('id', 'some-long-guid'));
    }

    public function testNameIsInArrayIfSpecified()
    {
        // given
        when($this->_mockRequestPattern->toArray())->return(array());
        when($this->_mockResponseDefinition->toArray())->return(array());
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, null, 'stub-name');

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('name', 'stub-name'));
    }

    public function testPriorityIsInArrayIfSpecified()
    {
        // given
        when($this->_mockRequestPattern->toArray())->return(array());
        when($this->_mockResponseDefinition->toArray())->return(array());
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, null, null, 5);

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('priority', 5));
    }

    public function testScenarioArrayIsMergedIntoArrayIfSpecified()
    {
        // given
        when($this->_mockRequestPattern->toArray())->return(array());
        when($this->_mockResponseDefinition->toArray())->return(array());
        $scenarioMapping = new ScenarioMapping('Some Scenario', 'from', 'to');
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, null, null, null, $scenarioMapping);

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('scenarioName', 'Some Scenario'));
    }
}
