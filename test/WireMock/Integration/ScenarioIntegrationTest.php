<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;
use WireMock\Stubbing\Scenario;

require_once 'WireMockIntegrationTest.php';

class ScenarioIntegrationTest extends WireMockIntegrationTest
{
    public function testScenarioNameOfStubCanBeSet() {
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

    public function testRequiredScenarioStateOfStubCanBeSet() {
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

    public function testNewScenarioStateOfStubCanBeSet() {
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

    public function testScenariosCanBeReset() {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))->inScenario('Some Scenario')
                ->whenScenarioStateIs(Scenario::STARTED)
                ->willReturn(WireMock::aResponse()->withBody('Initial'))
                ->willSetStateTo('Another State'));
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))->inScenario('Some Scenario')
                ->whenScenarioStateIs('Another State')
                ->willReturn(WireMock::aResponse()->withBody('Modified')));

        // when
        $firstResponse = $this->_testClient->get('/some/url');
        $secondResponse = $this->_testClient->get('/some/url');
        self::$_wireMock->resetAllScenarios();
        $thirdResponse = $this->_testClient->get('/some/url');

        // then
        assertThat($firstResponse, is('Initial'));
        assertThat($secondResponse, is('Modified'));
        assertThat($thirdResponse, is('Initial'));
    }
}
