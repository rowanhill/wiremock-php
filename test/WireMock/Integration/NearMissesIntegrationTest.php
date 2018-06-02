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
        $nearMissesResult = self::$_wireMock->findNearMissesFor($loggedRequest);

        // then
        $nearMisses = $nearMissesResult->getNearMisses();
        $nearMiss = $nearMisses[0];
        assertThat($nearMiss->getRequest()->getUrl(), equalTo('/different-url'));
        assertThat($nearMiss->getMapping()->getRequest()->getUrlMatchingStrategy()->getMatchingValue(), equalTo('/some-url'));
        assertThat($nearMiss->getMatchResult()->getDistance(), floatValue());
    }
}