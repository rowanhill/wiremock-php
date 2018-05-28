<?php

namespace WireMock\Integration;

use DateInterval;
use DateTime;
use DateTimeZone;
use WireMock\Client\LoggedRequest;
use WireMock\Client\WireMock;

require_once 'WireMockIntegrationTest.php';

class RequestsIntegrationTest extends WireMockIntegrationTest
{
    public function testGettingAllServeEventsReturnsAllRequestDetails()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/matched'))
            ->willReturn(WireMock::aResponse()));
        $this->_testClient->get('/matched');
        $this->_testClient->get('/unmatched');

        // when
        $serveEvents = self::$_wireMock->getAllServeEvents();

        // then
        assertThat($serveEvents['requests'], arrayWithSize(2));
        assertThat($serveEvents['requests'][0]['request']['url'], equalTo('/unmatched'));
        assertThat($serveEvents['requests'][1]['request']['url'], equalTo('/matched'));
    }

    public function testGettingAllServeEventsSinceATime()
    {
        // given
        $this->_testClient->get('/unmatched');
        $oneMinuteAgo = new DateTime('now', new DateTimeZone('UTC'));
        $oneMinuteAgo->sub(new DateInterval('PT1M'));

        // when
        $serveEvents = self::$_wireMock->getAllServeEvents($oneMinuteAgo);

        // then
        assertThat($serveEvents['requests'], arrayWithSize(1));
    }

    public function testGettingAllServeEventsCanBeLimited()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/matched'))
            ->willReturn(WireMock::aResponse()));
        $this->_testClient->get('/matched');
        $this->_testClient->get('/unmatched');

        // when
        $serveEvents = self::$_wireMock->getAllServeEvents(null, 1);

        // then
        assertThat($serveEvents['requests'], arrayWithSize(1));
        assertThat($serveEvents['requests'][0]['request']['url'], equalTo('/unmatched'));
    }

    public function testFindingAllRequestsReturnsMatchingRequestDetails()
    {
        // given
        $this->_testClient->get('/some/url', array('Cookie: foo=bar'));

        // when
        $requests = self::$_wireMock->findAll(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));

        // then
        assertThat($requests, is(arrayWithSize(1)));
        /** @var LoggedRequest $request */
        $request = current($requests);
        assertThat($request->getUrl(), is('/some/url'));
    }

    public function testGettingUnmatchedRequests()
    {
        // given
        $this->_testClient->get('/unmatched');

        // when
        $unmatchedRequests = self::$_wireMock->findUnmatchedRequests();

        // then
        assertThat($unmatchedRequests->getRequestJournalDisabled(), is(false));
        $loggedRequests = $unmatchedRequests->getRequests();
        assertThat($loggedRequests, arrayWithSize(1));
        assertThat($loggedRequests[0]->getUrl(), equalTo('/unmatched'));
    }

    public function testResettingAllRequestsRemovesPreviousRequestsFromTheJournal()
    {
        // given
        $this->_testClient->get('/unmatched');

        // when
        self::$_wireMock->resetAllRequests();

        // then
        $serveEvents = self::$_wireMock->getAllServeEvents();
        assertThat($serveEvents['requests'], arrayWithSize(0));
    }
}