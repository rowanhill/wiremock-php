<?php

namespace WireMock\Integration;

use WireMock\Client\MultipartValuePattern;
use WireMock\Client\WireMock;

class MultipartIntegrationTest extends WireMockIntegrationTest
{
    public function testSimpleMultipartStubCanBeRegistered()
    {
        // when
        $stubbing = self::$_wireMock->stubFor(WireMock::post(WireMock::anyUrl())
            ->withMultipartRequestBody(WireMock::aMultipart()->withMultipartBody(WireMock::matching('abc')))
            ->withMultipartRequestBody(WireMock::aMultipart()->withMultipartBody(WireMock::matching('def')))
            ->willReturn(WireMock::aResponse())
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubbing);
        assertThat($stubbing->getRequest()->getMultipartPatterns(), arrayWithSize(2));
        $patterns = $stubbing->getRequest()->getMultipartPatterns();
        assertThat($patterns[0]['bodyPatterns'][0], equalTo(array('matches' => 'abc')));
        assertThat($patterns[1]['bodyPatterns'][0], equalTo(array('matches' => 'def')));
    }

    public function testMultipartWithNameCanBeRegistered()
    {
        // when
        $stubbing = self::$_wireMock->stubFor(WireMock::post(WireMock::anyUrl())
            ->withMultipartRequestBody(WireMock::aMultipart()->withName('partName'))
            ->willReturn(WireMock::aResponse())
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubbing);
        $patterns = $stubbing->getRequest()->getMultipartPatterns();
        assertThat($patterns[0]['headers']['Content-Disposition'], equalTo(array('contains' => 'name="partName"')));
    }

    public function testMultipartWithHeaderCanBeRegistered()
    {
        // when
        $stubbing = self::$_wireMock->stubFor(WireMock::post(WireMock::anyUrl())
            ->withMultipartRequestBody(WireMock::aMultipart()->withHeader('X-Header', WireMock::containing('foo')))
            ->willReturn(WireMock::aResponse())
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubbing);
        $patterns = $stubbing->getRequest()->getMultipartPatterns();
        assertThat($patterns[0]['headers']['X-Header'], equalTo(array('contains' => 'foo')));
    }

    public function testMultipartMatchingTypeDefaultsToAny()
    {
        // when
        $stubbing = self::$_wireMock->stubFor(WireMock::post(WireMock::anyUrl())
            ->withMultipartRequestBody(WireMock::aMultipart())
            ->willReturn(WireMock::aResponse())
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubbing);
        $patterns = $stubbing->getRequest()->getMultipartPatterns();
        assertThat($patterns[0]['matchingType'], equalTo(MultipartValuePattern::ANY));
    }

    public function testMultipartMatchingTypeCanBetSetToAll()
    {
        // when
        $stubbing = self::$_wireMock->stubFor(WireMock::post(WireMock::anyUrl())
            ->withMultipartRequestBody(WireMock::aMultipart()->matchingType(MultipartValuePattern::ALL))
            ->willReturn(WireMock::aResponse())
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubbing);
        $patterns = $stubbing->getRequest()->getMultipartPatterns();
        assertThat($patterns[0]['matchingType'], equalTo(MultipartValuePattern::ALL));
    }
}