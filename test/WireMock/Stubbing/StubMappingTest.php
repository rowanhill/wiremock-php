<?php

namespace WireMock\Stubbing;

use Phake;
use WireMock\Client\WireMock;
use WireMock\HamcrestTestCase;
use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;
use WireMock\Serde\SerializerFactory;

class StubMappingTest extends HamcrestTestCase
{
    /** @var RequestPattern */
    private $_mockRequestPattern;
    /** @var ResponseDefinition */
    private $_mockResponseDefinition;
    private $_serializer;

    public function setUp(): void
    {
        $this->_serializer = SerializerFactory::default();
        $this->_mockRequestPattern = Phake::mock(RequestPattern::class);
        $this->_mockResponseDefinition = Phake::mock(ResponseDefinition::class);
    }

    private function toArray($obj)
    {
        return $this->_serializer->normalize($obj);
    }

    public function testRequestPatternAndResponseDefinitionAreAvailableInArray()
    {
        // given
        $stubMapping = new StubMapping(
            new RequestPattern('GET', WireMock::anyUrl()),
            new ResponseDefinition(200)
        );

        // when
        $stubMappingArray = $this->toArray($stubMapping);

        // then
        assertThat($stubMappingArray, hasEntry('request', $this->toArray($stubMapping->getRequest())));
        assertThat($stubMappingArray, hasEntry('response', $this->toArray($stubMapping->getResponse())));
    }

    public function testIdIsInArrayIfSpecified()
    {
        // given
        $stubMapping = new StubMapping(
            new RequestPattern('GET', WireMock::anyUrl()),
            new ResponseDefinition(200),
            'some-long-guid'
        );

        // when
        $stubMappingArray = $this->toArray($stubMapping);

        // then
        assertThat($stubMappingArray, hasEntry('id', 'some-long-guid'));
    }

    public function testNameIsInArrayIfSpecified()
    {
        // given
        $stubMapping = new StubMapping(
            new RequestPattern('GET', WireMock::anyUrl()),
            new ResponseDefinition(200),
            null,
            'stub-name'
        );

        // when
        $stubMappingArray = $this->toArray($stubMapping);

        // then
        assertThat($stubMappingArray, hasEntry('name', 'stub-name'));
    }

    public function testPriorityIsInArrayIfSpecified()
    {
        // given
        $stubMapping = new StubMapping(
            new RequestPattern('GET', WireMock::anyUrl()),
            new ResponseDefinition(200),
            null,
            null,
            5
        );

        // when
        $stubMappingArray = $this->toArray($stubMapping);

        // then
        assertThat($stubMappingArray, hasEntry('priority', 5));
    }

    public function testScenarioArrayIsMergedIntoArrayIfSpecified()
    {
        // given
        $scenarioMapping = new ScenarioMapping('Some Scenario', 'from', 'to');
        $stubMapping = new StubMapping(
            new RequestPattern('GET', WireMock::anyUrl()),
            new ResponseDefinition(200),
            null,
            null,
            null,
            $scenarioMapping
        );

        // when
        $stubMappingArray = $this->toArray($stubMapping);

        // then
        assertThat($stubMappingArray, hasEntry('scenarioName', 'Some Scenario'));
    }
}
