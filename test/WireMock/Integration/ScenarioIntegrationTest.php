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

    public function testSingleScenarioCanBeReset()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/one'))->inScenario('scenario1')
            ->whenScenarioStateIs(Scenario::STARTED)
            ->willReturn(WireMock::aResponse()->withBody('1-a'))
            ->willSetStateTo('scenario1-modified-state'));
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/one'))->inScenario('scenario1')
            ->whenScenarioStateIs('scenario1-modified-state')
            ->willReturn(WireMock::aResponse()->withBody('1-b')));
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/two'))->inScenario('scenario2')
            ->whenScenarioStateIs(Scenario::STARTED)
            ->willReturn(WireMock::aResponse()->withBody('2-a'))
            ->willSetStateTo('scenario2-modified-state'));
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/two'))->inScenario('scenario2')
            ->whenScenarioStateIs('scenario2-modified-state')
            ->willReturn(WireMock::aResponse()->withBody('2-b')));

        // when
        $this->_testClient->get('/one');
        $this->_testClient->get('/two');
        self::$_wireMock->resetScenario('scenario1');
        $scenarioOneResponse = $this->_testClient->get('/one');
        $scenarioTwoResponse = $this->_testClient->get('/two');

        // then
        assertThat($scenarioOneResponse, is('1-a'));
        assertThat($scenarioTwoResponse, is('2-b'));
    }

    public function testScenarioStateCanBeSetDirectly()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/one'))->inScenario('scenario1')
            ->whenScenarioStateIs(Scenario::STARTED)
            ->willReturn(WireMock::aResponse()->withBody('1-a'))
            ->willSetStateTo('scenario1-modified-state'));
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/one'))->inScenario('scenario1')
            ->whenScenarioStateIs('scenario1-modified-state')
            ->willReturn(WireMock::aResponse()->withBody('1-b')));

        // when
        self::$_wireMock->setScenarioState('scenario1', 'scenario1-modified-state');
        $response = $this->_testClient->get('/one');

        // then
        assertThat($response, is('1-b'));
    }
}
