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
        assertThat($nearMiss->getRequestPattern(), nullValue());
        assertThat($nearMiss->getMatchResult()->getDistance(), floatValue());
    }

    public function testFindNearMissesForRequestPattern()
    {
        // given
        $requestPattern = WireMock::getRequestedFor(WireMock::urlEqualTo('/some-url'));
        $this->_testClient->get('/different-url');

        // when
        $nearMissesResult = self::$_wireMock->findNearMissesFor($requestPattern);

        // then
        $nearMisses = $nearMissesResult->getNearMisses();
        $nearMiss = $nearMisses[0];
        assertThat($nearMiss->getRequest()->getUrl(), equalTo('/different-url'));
        assertThat($nearMiss->getMapping(), nullValue());
        assertThat($nearMiss->getRequestPattern()->getUrlMatchingStrategy()->getMatchingValue(), equalTo('/some-url'));
        assertThat($nearMiss->getMatchResult()->getDistance(), floatValue());
    }

    public function testFindNearMissesForAllUnmatched()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some-url1'))
            ->willReturn(WireMock::aResponse()));
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some-url2'))
            ->willReturn(WireMock::aResponse()));
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some-url3'))
            ->willReturn(WireMock::aResponse()));
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some-url4'))
            ->willReturn(WireMock::aResponse()));
        $this->_testClient->get('/different-url');

        // when
        $nearMissesResult = self::$_wireMock->findNearMissesForAllUnmatched();

        // then
        $nearMisses = $nearMissesResult->getNearMisses();
        assertThat($nearMisses, arrayWithSize(3));
        $nearMiss = $nearMisses[0];
        assertThat($nearMiss->getRequest()->getUrl(), equalTo('/different-url'));
        assertThat($nearMiss->getMapping()->getRequest()->getUrlMatchingStrategy()->getMatchingValue(), startsWith('/some-url'));
        assertThat($nearMiss->getRequestPattern(), nullValue());
        assertThat($nearMiss->getMatchResult()->getDistance(), floatValue());
    }
}