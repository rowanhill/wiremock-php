<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;

require_once 'WireMockIntegrationTest.php';

class MappingsIntegrationTest extends WireMockIntegrationTest
{
    public function testMappingsListIsEmptyIfNoStubsHaveBeenMade()
    {
        // when
        $mappings = self::$_wireMock->listAllStubMappings();

        // then
        assertThat($mappings->getMappings(), emptyArray());
    }

    public function testMappingListContainsStubsPreviouslyCreated()
    {
        // given
        $mapping = self::$_wireMock->stubFor(WireMock::any(WireMock::anyUrl())->willReturn(WireMock::aResponse()));

        // when
        $mappings = self::$_wireMock->listAllStubMappings();

        // then
        assertThat($mappings->getMappings(), hasItemInArray($mapping));
    }

    public function testMappingListCanBeLimitedToMostRecent()
    {
        // given
        self::$_wireMock->stubFor(WireMock::any(WireMock::urlEqualTo('/one'))
            ->willReturn(WireMock::aResponse()));
        $mapping = self::$_wireMock->stubFor(WireMock::any(WireMock::urlEqualTo('/two'))
            ->willReturn(WireMock::aResponse()));

        // when
        $mappings = self::$_wireMock->listAllStubMappings(1);

        // then
        assertThat($mappings->getMappings(), allOf(hasItemInArray($mapping), arrayWithSize(1)));
    }

    public function testMappingListCanBeOffsetToRetrieveOlderStubs()
    {
        // given
        $mapping = self::$_wireMock->stubFor(WireMock::any(WireMock::urlEqualTo('/one'))
            ->willReturn(WireMock::aResponse()));
        self::$_wireMock->stubFor(WireMock::any(WireMock::urlEqualTo('/two'))
            ->willReturn(WireMock::aResponse()));

        // when
        $mappings = self::$_wireMock->listAllStubMappings(1, 1);

        // then
        assertThat($mappings->getMappings(), allOf(hasItemInArray($mapping), arrayWithSize(1)));
    }

    public function testGettingSingleMappingRetrievesStubById()
    {
        // given
        $mapping = self::$_wireMock->stubFor(WireMock::any(WireMock::urlEqualTo('/one'))
            ->willReturn(WireMock::aResponse()));

        // when
        $returnedMapping = self::$_wireMock->getSingleStubMapping($mapping->getId());

        // then
        assertThat($returnedMapping, equalTo($mapping));
    }
}