<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;
use WireMock\Stubbing\Scenario;

class ScenarioIntegrationTest extends WireMockIntegrationTest
{
    public function testScenarioNameOfStubCanBeSet()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->inScenario('Some Scenario')
            ->willReturn(WireMock::aResponse()->withBody('Some body'))
        );

        // then
        assertThat($stubMapping->getScenarioName(), is('Some Scenario'));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testRequiredScenarioStateOfStubCanBeSet()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->inScenario('Some Scenario')
                ->whenScenarioStateIs('Some State')
                ->willReturn(WireMock::aResponse()->withBody('Some body'))
        );

        // then
        assertThat($stubMapping->getRequiredScenarioState(), is('Some State'));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testNewScenarioStateOfStubCanBeSet()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->inScenario('Some Scenario')
                ->willReturn(WireMock::aResponse()->withBody('Some body'))
                ->willSetStateTo('Another State')
        );

        // then
        assertThat($stubMapping->getNewScenarioState(), is('Another State'));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testScenariosCanBeReset()
    {
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

    public function testGettingScenarios()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->inScenario('Some Scenario')
            ->willReturn(WireMock::aResponse()->withBody('Some body'))
            ->willSetStateTo('Another State')
        );
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->inScenario('Some Scenario')
            ->whenScenarioStateIs('Another State')
            ->willReturn(WireMock::aResponse()->withBody('Modified'))
        );

        // when
        $scenariosResult = self::$_wireMock->getAllScenarios();

        // then
        assertThat($scenariosResult->getScenarios(), arrayWithSize(1));
        $scenarios = $scenariosResult->getScenarios();
        assertThat($scenarios[0]->getId(), stringValue());
        assertThat($scenarios[0]->getName(), equalTo('Some Scenario'));
        assertThat($scenarios[0]->getState(), equalTo(Scenario::STARTED));
        assertThat($scenarios[0]->getPossibleStates(), equalTo(array('Another State')));
    }
}
