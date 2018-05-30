<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;
use WireMock\Stubbing\Fault;

require_once 'WireMockIntegrationTest.php';

class FaultsAndDelaysIntegrationTest extends WireMockIntegrationTest
{
    public function testFixedDelayOnStubbedResponseCanBeSpecified()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()
                ->withFixedDelay(2000))
        );

        // then
        $stubMappingArray = $stubMapping->toArray();
        assertThat($stubMappingArray['response']['fixedDelayMilliseconds'], is(2000));
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

    private function _testFaultCanBeStubbed($fault)
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()
                    ->withFault($fault))
        );

        // then
        $stubMappingArray = $stubMapping->toArray();
        assertThat($stubMappingArray['response']['fault'], is($fault));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }
}
