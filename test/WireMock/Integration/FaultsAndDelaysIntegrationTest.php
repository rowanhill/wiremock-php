<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;
use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\LogNormal;
use WireMock\Fault\UniformDistribution;
use WireMock\Stubbing\Fault;

class FaultsAndDelaysIntegrationTest extends WireMockIntegrationTest
{
    protected function tearDown(): void
    {
        parent::tearDown();
        self::$_wireMock->resetGlobalDelays();
    }

    public function testFixedDelayOnStubbedResponseCanBeSpecified()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()
                ->withFixedDelay(2000))
        );

        // then
        assertThat($stubMapping->getResponse()->getFixedDelayMillis(), is(2000));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testLogNormalDelayOnStubbedResponseCanBeSpecified()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()
                ->withLogNormalRandomDelay(90, 0.1))
        );

        // then
        assertThat($stubMapping->getResponse()->getRandomDelayDistribution(),
            equalTo(new LogNormal(90, 0.1)));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testUniformDelayOnStubbedResponseCanBeSpecified()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()
                ->withUniformRandomDelay(15, 25))
        );

        // then
        assertThat($stubMapping->getResponse()->getRandomDelayDistribution(),
            equalTo(new UniformDistribution(15, 25)));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testRandomDelayOnStubbedResponseCanBeSpecified()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withRandomDelay(new UniformDistribution(15, 25)))
        );

        // then
        assertThat($stubMapping->getResponse()->getRandomDelayDistribution(),
            equalTo(new UniformDistribution(15, 25)));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testChunkedDribbleDelayOnStubbedResponseCanBeSpecified()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withChunkedDribbleDelay(5, 1000))
        );

        // then
        assertThat($stubMapping->getResponse()->getChunkedDribbleDelay(),
            equalTo(new ChunkedDribbleDelay(5, 1000)));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testGlobalFixedDelayOnStubbedResponsesCanBeSet()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()));
        $this->_testClient->get('/some/url');
        assertThat($this->_testClient->getLastRequestTimeMillis(), lessThan(1000));

        // when
        self::$_wireMock->setGlobalFixedDelay(1000);
        $this->_testClient->get('/some/url');

        // then
        assertThat($this->_testClient->getLastRequestTimeMillis(), greaterThan(1000));
    }

    public function testGlobalRandomDelayOnStubbedResponsesCanBeSet()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()));
        $this->_testClient->get('/some/url');
        assertThat($this->_testClient->getLastRequestTimeMillis(), lessThan(1000));

        // when
        self::$_wireMock->setGlobalRandomDelay(new UniformDistribution(1000, 1010));
        $this->_testClient->get('/some/url');

        // then
        assertThat($this->_testClient->getLastRequestTimeMillis(), greaterThan(1000));
    }

    public function testEmptyResponseFaultCanBeStubbed()
    {
        $this->_testFaultCanBeStubbed(Fault::EMPTY_RESPONSE);
    }

    public function testMalformedResponseChunkFaultCanBeStubbed()
    {
        $this->_testFaultCanBeStubbed(Fault::MALFORMED_RESPONSE_CHUNK);
    }

    public function testRandomDataThenCloseFaultCanBeStubbed()
    {
        $this->_testFaultCanBeStubbed(Fault::RANDOM_DATA_THEN_CLOSE);
    }

    public function testConnectionResetByPeerThenCloseFaultCanBeStubbed()
    {
        $this->_testFaultCanBeStubbed(Fault::CONNECTION_RESET_BY_PEER);
    }

    private function _testFaultCanBeStubbed($fault)
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()
                    ->withFault($fault))
        );

        // then
        assertThat($stubMapping->getResponse()->getFault(), is($fault));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }
}
