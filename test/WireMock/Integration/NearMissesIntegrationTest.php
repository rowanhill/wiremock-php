<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;

require_once 'WireMockIntegrationTest.php';

class NearMissesIntegrationTest extends WireMockIntegrationTest
{
    public function testFindNearMissesForLoggedRequest()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some-url'))
            ->willReturn(WireMock::aResponse()));
        $this->_testClient->get('/different-url');
        $loggedRequests = self::$_wireMock->findUnmatchedRequests()->getRequests();
        $loggedRequest = $loggedRequests[0];

        // when
        $nearMisses = self::$_wireMock->findNearMissesFor($loggedRequest);

        // then
        assertThat($nearMisses['nearMisses'][0]['request']['url'], equalTo('/different-url'));
        assertThat($nearMisses['nearMisses'][0]['stubMapping']['request']['url'], equalTo('/some-url'));
        assertThat($nearMisses['nearMisses'][0]['matchResult']['distance'], floatValue());
    }
}