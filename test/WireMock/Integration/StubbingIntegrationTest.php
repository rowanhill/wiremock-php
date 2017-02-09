<?php

namespace WireMock\Integration;

require_once 'WireMockIntegrationTest.php';

use WireMock\Client\JsonValueMatchingStrategy;
use WireMock\Client\WireMock;

class StubbingIntegrationTest extends WireMockIntegrationTest
{
    public function clearMappings()
    {
        exec('rm -f ../wiremock/mappings/*');
    }

    public function setUp()
    {
        parent::setUp();
        $this->clearMappings();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->clearMappings();
    }

    public function testRequestWithUrlAndStringBodyCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBody('Here is some body text')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testRequestUrlAndBodyFileCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBodyFile('someFile.html')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testRequestWithBinaryBodyCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBodyData('some binary data as a string')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testRequestUrlCanBeMatchedByRegex()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlMatching('/some/.+'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testMappingsCanBeReset()
    {
        // given
        $wiremock = WireMock::create();
        $wiremock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()));

        // when
        $wiremock->reset();

        // then
        assertThatThereAreNoMappings();
    }

    public function testMappingsCanBeResetToDefault()
    {
        // given
        $wiremock = WireMock::create();
        $wiremock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()));

        // when
        $wiremock->resetToDefault();

        // then
        assertThatThereAreNoMappings();
    }

    public function testHeadersCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withHeader('Content-Type', 'text/plain')
                ->withBody('Here is some body text')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testHeadersCanBeMatched()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withHeader('Cookie', WireMock::equalTo('foo=bar'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStatusCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withStatus(403)));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByBodyMatching()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::matching('<status>OK</status>'))
            ->withRequestBody(WireMock::notMatching('.*ERROR.*'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByBodyContaining()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::containing('ERROR'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByBodyEqualingJson()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::equalToJson('{"key":"value"}'))
            ->withRequestBody(WireMock::equalToJson('{}', JsonValueMatchingStrategy::COMPARE_MODE__LENIENT))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByBodyJsonPath()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::matchingJsonPath('$.status'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStubPriorityCanBeSet()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->atPriority(5)
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStubsCanBeSaved()
    {
        // given
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBody('Here is some body text')));

        // when
        self::$_wireMock->saveAllMappings();
        self::tearDownAfterClass(); // shut down the server
        self::setUpBeforeClass(); // start the server again

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }
}
