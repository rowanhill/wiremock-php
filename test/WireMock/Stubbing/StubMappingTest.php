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
    /** @var Scenario */
    private $_mockScenario;

    public function setUp()
    {
        $this->_mockRequestPattern = mock('WireMock\Matching\RequestPattern');
        $this->_mockResponseDefinition = mock('WireMock\Http\ResponseDefinition');
        $this->_mockScenario = mock('WireMock\Stubbing\Scenario');
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

    public function testPriorityIsInArrayIfSpecified()
    {
        // given
        when($this->_mockRequestPattern->toArray())->return(array());
        when($this->_mockResponseDefinition->toArray())->return(array());
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, 5);

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
        when($this->_mockScenario->toArray())->return(array('scenarioName' => 'Some Scenario'));
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, null,
            $this->_mockScenario);

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('scenarioName', 'Some Scenario'));
    }
}
