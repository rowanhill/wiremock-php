<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;
use WireMock\Recording\RecordingStatusResult;
use WireMock\Recording\RecordSpec;

require_once 'WireMockIntegrationTest.php';

class RecordingIntegrationTest extends WireMockIntegrationTest
{
    /** @var WireMock */
    protected static $_wireMock2;

    public static function setUpBeforeClass()
    {
        self::runCmd('./../wiremock/start.sh 1 8080');
        self::runCmd('./../wiremock/start.sh 2 8082');
        self::$_wireMock = WireMock::create();
        assertThat(self::$_wireMock->isAlive(), is(true));
        self::$_wireMock2 = WireMock::create('localhost', 8082);
        assertThat(self::$_wireMock2->isAlive(), is(true));
    }

    public static function tearDownAfterClass()
    {
        self::runCmd('./../wiremock/stop.sh 1');
        self::runCmd('./../wiremock/stop.sh 2');
    }

    public function setUp()
    {
        parent::setUp();
        self::$_wireMock2->reset();
        $this->clearMappings();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->clearMappings();
    }
    
    public function testRecordingStatusDefaultsToNeverStarted()
    {
        // when
        $recordingStatus = self::$_wireMock->getRecordingStatus();

        // then
        assertThat($recordingStatus->getStatus(), equalTo(RecordingStatusResult::NEVER_STARTED));
    }

    public function testRecordingCanBeStarted()
    {
        // when
        self::$_wireMock->startRecording('http://localhost:8082/');

        // then
        $recordingStatus = self::$_wireMock->getRecordingStatus();
        assertThat($recordingStatus->getStatus(), equalTo(RecordingStatusResult::RECORDING));
    }

    public function testRecordingCanBeStopped()
    {
        // given
        self::$_wireMock->startRecording('http://localhost:8082/');

        // when
        self::$_wireMock->stopRecording();

        // then
        $recordingStatus = self::$_wireMock->getRecordingStatus();
        assertThat($recordingStatus->getStatus(), equalTo(RecordingStatusResult::STOPPED));
    }

    public function testRecordedRequestsAreReturnedWhenStopping()
    {
        // given
        self::$_wireMock2->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->willReturn(WireMock::aResponse()->withBody('Some Body')));
        self::$_wireMock->startRecording('http://localhost:8082/');
        $this->_testClient->get('/recordables/123');

        // when
        $result = self::$_wireMock->stopRecording();

        // then
        assertThat($result->getMappings(), arrayWithSize(1));
        $mappings = $result->getMappings();
        assertThat($mappings[0]->getResponse()->getBase64Body(), equalTo(base64_encode('Some Body')));
    }

    public function testRecordedRequestsMatchingRecordingSpecAreReturnedWhenStopping()
    {
        // given
        self::$_wireMock2->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->willReturn(WireMock::aResponse()->withBody('Some Body')));
        self::$_wireMock->startRecording(Wiremock::recordSpec()
            ->forTarget('http://localhost:8082/')
            ->onlyRequestsMatching(WireMock::getRequestedFor(WireMock::urlPathMatching('/recordables/.*')))
            ->matchRequestBodyWithEqualToJson()
        );
        $this->_testClient->get('/recordables/123');

        // when
        $result = self::$_wireMock->stopRecording();

        // then
        assertThat($result->getMappings(), arrayWithSize(1));
        $mappings = $result->getMappings();
        assertThat($mappings[0]->getResponse()->getBase64Body(), equalTo(base64_encode('Some Body')));
    }

    public function testPreviouslyIssuedRequestsCanBeSnapshotted()
    {
        // given
        self::$_wireMock2->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->willReturn(WireMock::aResponse()->withBody('Some Body')));
        self::$_wireMock->stubFor(WireMock::any(WireMock::anyUrl())
            ->willReturn(WireMock::aResponse()->proxiedFrom('http://localhost:8082/')));
        $this->_testClient->get('/recordables/123');

        // when
        $result = self::$_wireMock->snapshotRecord();

        // then
        assertThat($result->getMappings(), arrayWithSize(1));
        $mappings = $result->getMappings();
        assertThat($mappings[0]->getResponse()->getBase64Body(), equalTo(base64_encode('Some Body')));
    }

    public function testPreviouslyIssuedRequestsMatchingRecordingSpecCanBeSnapshotted()
    {
        // given
        self::$_wireMock2->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->willReturn(WireMock::aResponse()->withBody('Some Body')));
        self::$_wireMock->stubFor(WireMock::any(WireMock::anyUrl())
            ->willReturn(WireMock::aResponse()->proxiedFrom('http://localhost:8082/')));
        $this->_testClient->get('/recordables/123');

        // when
        $result = self::$_wireMock->snapshotRecord(Wiremock::recordSpec()
            ->forTarget('http://localhost:8082/')
            ->onlyRequestsMatching(WireMock::getRequestedFor(WireMock::urlPathMatching('/recordables/.*')))
            ->matchRequestBodyWithEqualToJson()
        );

        // then
        assertThat($result->getMappings(), arrayWithSize(1));
        $mappings = $result->getMappings();
        assertThat($mappings[0]->getResponse()->getBase64Body(), equalTo(base64_encode('Some Body')));
    }

    public function testStartAndStopRecordingWithIdsOutputFormat()
    {
        // given
        self::$_wireMock2->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->willReturn(WireMock::aResponse()->withBody('Some Body')));
        self::$_wireMock->startRecording(Wiremock::recordSpec()
            ->forTarget('http://localhost:8082/')
            ->withOutputFormat(RecordSpec::IDS)
        );
        $this->_testClient->get('/recordables/123');

        // when
        $result = self::$_wireMock->stopRecording();

        // then
        assertThat($result->getIds(), arrayWithSize(1));
    }

    public function testSnapshotRecordingWithIdsOutputFormat()
    {
        // given
        self::$_wireMock2->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->willReturn(WireMock::aResponse()->withBody('Some Body')));
        self::$_wireMock->stubFor(WireMock::any(WireMock::anyUrl())
            ->willReturn(WireMock::aResponse()->proxiedFrom('http://localhost:8082/')));
        $this->_testClient->get('/recordables/123');

        // when
        $result = self::$_wireMock->snapshotRecord(Wiremock::recordSpec()
            ->forTarget('http://localhost:8082/')
            ->withOutputFormat(RecordSpec::IDS));

        // then
        assertThat($result->getIds(), arrayWithSize(1));
    }

    public function testStartingRecordDoesNotRecordNonProxiedRequestsByDefault()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->atPriority(1)
            ->willReturn(WireMock::aResponse())
        );
        self::$_wireMock->startRecording(Wiremock::recordSpec()
            ->forTarget('http://localhost:8082/')
        );
        $this->_testClient->get('/recordables/123');

        // when
        $result = self::$_wireMock->stopRecording();

        // then
        assertThat($result->getMappings(), arrayWithSize(0));
    }

    public function testStartingRecordCanRecordNonProxiedRequestsIfAllowed()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->atPriority(1)
            ->willReturn(WireMock::aResponse())
        );
        self::$_wireMock->startRecording(Wiremock::recordSpec()
            ->forTarget('http://localhost:8082/')
            ->allowNonProxied(true)
        );
        $this->_testClient->get('/recordables/123');

        // when
        $result = self::$_wireMock->stopRecording();

        // then
        assertThat($result->getMappings(), arrayWithSize(1));
    }

    public function testRecordedMappingsCanBeRequestedById()
    {
        // given
        self::$_wireMock2->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->willReturn(WireMock::aResponse()->withBody('Some Body')));
        self::$_wireMock->stubFor(WireMock::any(WireMock::anyUrl())
            ->willReturn(WireMock::aResponse()->proxiedFrom('http://localhost:8082/')));
        $this->_testClient->get('/recordables/123');
        $serveEvents = self::$_wireMock->getAllServeEvents()->getRequests();

        // when
        $result = self::$_wireMock->snapshotRecord(
            WireMock::recordSpec()
                ->onlyRequestIds(array($serveEvents[0]->getId()))
        );

        // then
        assertThat($result->getMappings(), arrayWithSize(1));
    }

    public function testRecordedMappingsCanBeExcludedById()
    {
        // given
        self::$_wireMock2->stubFor(WireMock::get(WireMock::urlPathEqualTo('/recordables/123'))
            ->willReturn(WireMock::aResponse()->withBody('Some Body')));
        self::$_wireMock->stubFor(WireMock::any(WireMock::anyUrl())
            ->willReturn(WireMock::aResponse()->proxiedFrom('http://localhost:8082/')));
        $this->_testClient->get('/recordables/123');
        $serveEvents = self::$_wireMock->getAllServeEvents()->getRequests();

        // when
        $result = self::$_wireMock->snapshotRecord(
            WireMock::recordSpec()
                ->onlyRequestIds(array('98ae941f-37b6-46f5-81fd-026728c46080')) // shouldn't be the right ID
        );

        // then
        assertThat($result->getMappings(), arrayWithSize(0));
    }
}