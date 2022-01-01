<?php

namespace WireMock\Stubbing;

use Phake;
use WireMock\HamcrestTestCase;
use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;

class StubMappingTest extends HamcrestTestCase
{
    /** @var RequestPattern */
    private $_mockRequestPattern;
    /** @var ResponseDefinition */
    private $_mockResponseDefinition;

    public function setUp()
    {
        $this->_mockRequestPattern = Phake::mock(RequestPattern::class);
        $this->_mockResponseDefinition = Phake::mock(ResponseDefinition::class);
    }

    public function testRequestPatternAndResponseDefinitionAreAvailableInArray()
    {
        // given
        $requestArray = array('request');
        $responseArray = array('response');
        Phake::when($this->_mockRequestPattern)->toArray()->thenReturn($requestArray);
        Phake::when($this->_mockResponseDefinition)->toArray()->thenReturn($responseArray);
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
        Phake::when($this->_mockRequestPattern)->toArray()->thenReturn(array());
        Phake::when($this->_mockResponseDefinition)->toArray()->thenReturn(array());
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, 'some-long-guid');

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('id', 'some-long-guid'));
    }

    public function testNameIsInArrayIfSpecified()
    {
        // given
        Phake::when($this->_mockRequestPattern)->toArray()->thenReturn(array());
        Phake::when($this->_mockResponseDefinition)->toArray()->thenReturn(array());
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, null, 'stub-name');

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('name', 'stub-name'));
    }

    public function testPriorityIsInArrayIfSpecified()
    {
        // given
        Phake::when($this->_mockRequestPattern)->toArray()->thenReturn(array());
        Phake::when($this->_mockResponseDefinition)->toArray()->thenReturn(array());
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, null, null, 5);

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('priority', 5));
    }

    public function testScenarioArrayIsMergedIntoArrayIfSpecified()
    {
        // given
        Phake::when($this->_mockRequestPattern)->toArray()->thenReturn(array());
        Phake::when($this->_mockResponseDefinition)->toArray()->thenReturn(array());
        $scenarioMapping = new ScenarioMapping('Some Scenario', 'from', 'to');
        $stubMapping = new StubMapping($this->_mockRequestPattern, $this->_mockResponseDefinition, null, null, null, $scenarioMapping);

        // when
        $stubMappingArray = $stubMapping->toArray();

        // then
        assertThat($stubMappingArray, hasEntry('scenarioName', 'Some Scenario'));
    }
}
