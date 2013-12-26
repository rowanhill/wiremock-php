<?php

namespace WireMock\Client;

require_once 'WireMockIntegrationTest.php';

class ScenarioIntegrationTest extends WireMockIntegrationTest
{
    function testScenarioNameOfStubCanBeSet() {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->inScenario('Some Scenario')
            ->willReturn(WireMock::aResponse()->withBody('Some body'))
        );

        // then
        $stubMappingArray = $stubMapping->toArray();
        assertThat($stubMappingArray['scenarioName'], is('Some Scenario'));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    function testRequiredScenarioStateOfStubCanBeSet() {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->inScenario('Some Scenario')
                ->whenScenarioStateIs('Some State')
                ->willReturn(WireMock::aResponse()->withBody('Some body'))
        );

        // then
        $stubMappingArray = $stubMapping->toArray();
        assertThat($stubMappingArray['requiredScenarioState'], is('Some State'));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    function testNewScenarioStateOfStubCanBeSet() {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->inScenario('Some Scenario')
                ->willReturn(WireMock::aResponse()->withBody('Some body'))
                ->willSetStateTo('Another State')
        );

        // then
        $stubMappingArray = $stubMapping->toArray();
        assertThat($stubMappingArray['newScenarioState'], is('Another State'));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }
}