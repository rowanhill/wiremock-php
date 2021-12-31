<?php

namespace WireMock\Integration;

use DateInterval;
use DateTime;
use DateTimeZone;
use WireMock\Client\LoggedRequest;
use WireMock\Client\ServeEventQuery;
use WireMock\Client\WireMock;

class RequestsIntegrationTest extends WireMockIntegrationTest
{
    public function testGettingAllServeEventsReturnsAllRequestDetails()
    {
        // given
        $packedResponseBody = pack('c*', 0x23, 0x59, 0x11);
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/matched'))
            ->willReturn(WireMock::aResponse()
                ->withBodyData($packedResponseBody)
                ->withHeader('X-Hello', 'There')));
        $this->_testClient->get('/matched');
        $this->_testClient->post('/unmatched', http_build_query(array('requestBody' => 'hello')));

        // when
        $serveEvents = self::$_wireMock->getAllServeEvents();

        // then
        $loggedRequests = $serveEvents->getRequests();
        assertThat($serveEvents->getMeta()->getTotal(), equalTo(2));
        assertThat($loggedRequests, arrayWithSize(2));
        assertThat($loggedRequests[0]->getRequest()->getUrl(), equalTo('/unmatched'));
        assertThat($loggedRequests[0]->getRequest()->getBody(), equalTo('requestBody=hello'));
        assertThat($loggedRequests[1]->getRequest()->getUrl(), equalTo('/matched'));
        assertThat($loggedRequests[1]->getResponse()->getBody(), equalTo($packedResponseBody));
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
        assertThat($serveEvents->getRequests(), arrayWithSize(1));
    }

    public function testGettingAllServeEventsCanBeLimited()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/matched'))
            ->willReturn(WireMock::aResponse()));
        $this->_testClient->get('/matched');
        $this->_testClient->get('/unmatched');
        $this->_testClient->get('/unmatched2');

        // when
        $serveEvents = self::$_wireMock->getAllServeEvents(null, 1);

        // then
        $requests = $serveEvents->getRequests();
        assertThat($requests, arrayWithSize(1));
        assertThat($requests[0]->getRequest()->getUrl(), equalTo('/unmatched2'));
    }

    public function testGettingAllServeEventsCanBePaginatedWithServeEventQuery()
    {
        $this->_testClient->get('/unmatched');
        $this->_testClient->get('/unmatched2');
        $oneMinuteAgo = new DateTime('now', new DateTimeZone('UTC'));
        $oneMinuteAgo->sub(new DateInterval('PT1M'));

        // when
        $serveEvents = self::$_wireMock->getAllServeEvents(ServeEventQuery::paginated($oneMinuteAgo, 1));

        // then
        assertThat($serveEvents->getRequests(), arrayWithSize(1));
        assertThat($serveEvents->getRequests()[0]->getRequest()->getUrl(), equalTo('/unmatched2'));
    }

    public function testGettingUnmatchedServeEvents()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/matched'))
            ->willReturn(WireMock::aResponse()));
        $this->_testClient->get('/matched');
        $this->_testClient->get('/unmatched');
        $this->_testClient->get('/unmatched2');

        // when
        $serveEvents = self::$_wireMock->getAllServeEvents(ServeEventQuery::unmatched());

        // then
        $requests = $serveEvents->getRequests();
        assertThat($requests, arrayWithSize(2));
        assertThat($requests[0]->getRequest()->getUrl(), equalTo('/unmatched2'));
        assertThat($requests[1]->getRequest()->getUrl(), equalTo('/unmatched'));
    }

    public function testGettingServeEventsForStub()
    {
        // given
        $stub = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/matched'))
            ->willReturn(WireMock::aResponse()));
        $this->_testClient->get('/matched');
        $this->_testClient->get('/unmatched');

        // when
        $serveEvents = self::$_wireMock->getAllServeEvents(ServeEventQuery::forStubMapping($stub->getId()));

        // then
        $requests = $serveEvents->getRequests();
        assertThat($requests, arrayWithSize(1));
        assertThat($requests[0]->getRequest()->getUrl(), equalTo('/matched'));
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
        assertThat($serveEvents->getRequests(), arrayWithSize(0));
    }

    public function testRemovingServeEventRemovesRequestByIdFromTheJournal()
    {
        // given
        $this->_testClient->get('/unmatched');
        $this->_testClient->get('/unmatched2');
        $origServeEvents = self::$_wireMock->getAllServeEvents();
        $reqs = $origServeEvents->getRequests();
        $firstRequest = $reqs[0];
        $requestId = $firstRequest->getId();

        // when
        self::$_wireMock->removeServeEvent($requestId);

        // then
        $serveEvents = self::$_wireMock->getAllServeEvents();
        assertThat($serveEvents->getRequests(), arrayWithSize(1));
    }

    public function testRemovingServeEventsRemovesRequestsByMatcherFromTheJournal()
    {
        // given
        $this->_testClient->get('/unmatched');
        $this->_testClient->get('/unmatched2');

        // then
        self::$_wireMock->removeServeEvents(
            WireMock::getRequestedFor(WireMock::urlPathMatching('/unmatched2'))
        );

        // then
        $serveEvents = self::$_wireMock->getAllServeEvents();
        assertThat($serveEvents->getRequests(), arrayWithSize(1));
    }

    public function testRemoveEventsByStubMetadataRemovesMatchingRequestsFromTheJournal()
    {
        // given
        self::$_wireMock->stubFor(
            WireMock::get("/api/dosomething/123")
                ->withMetadata(array("tags" => array("test-57")))
                ->willReturn(WireMock::ok())
        );
        $this->_testClient->get('/unmatched');
        $this->_testClient->get('/api/dosomething/123');

        // then
        self::$_wireMock->removeEventsByStubMetadata(
            WireMock::matchingJsonPath("$.tags[0]", WireMock::equalTo("test-57"))
        );

        // then
        $serveEvents = self::$_wireMock->getAllServeEvents();
        assertThat($serveEvents->getRequests(), arrayWithSize(1));
    }
}
