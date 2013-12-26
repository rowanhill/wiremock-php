<?php

namespace WireMock\Integration;

use WireMock\Client\Curl;
use WireMock\Client\WireMock;
use WireMock\Stubbing\Scenario;

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

    function testScenariosCanBeReset() {
        // given
        self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))->inScenario('Some Scenario')
                ->whenScenarioStateIs(Scenario::STARTED)
                ->willReturn(WireMock::aResponse()->withBody('Initial'))
                ->willSetStateTo('Another State'));
        self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))->inScenario('Some Scenario')
                ->whenScenarioStateIs('Another State')
                ->willReturn(WireMock::aResponse()->withBody('Modified')));
        $curl = new Curl();

        // when
        $firstResponse = $curl->post('http://localhost:8080/some/url');
        $secondResponse = $curl->post('http://localhost:8080/some/url');
        self::$_wireMock->resetAllScenarios();
        $thirdResponse = $curl->post('http://localhost:8080/some/url');

        // then
        assertThat($firstResponse, is('Initial'));
        assertThat($secondResponse, is('Modified'));
        assertThat($thirdResponse, is('Initial'));
    }
}