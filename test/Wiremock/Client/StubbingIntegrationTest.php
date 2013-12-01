<?php

use WireMock\Client\WireMock;
use WireMock\Stubbing\StubMapping;

class StubbingIntegrationTest extends PHPUnit_Framework_TestCase
{
    /** @var WireMock */
    private static $_wireMock;

    static function setUpBeforeClass()
    {
        exec('cd ../wiremock && `java -jar wiremock-1.33-standalone.jar &> wiremock.log &`');
        self::$_wireMock = WireMock::create();
        assertThat(self::$_wireMock->isAlive(), is(true));
    }

    static function tearDownAfterClass()
    {
        $result = 0;
        $output = array();
        exec(
            "kill -9 `ps -e | grep \"java -jar wiremock-1.33-standalone.jar\" | grep -v grep | awk '{print $1}'`",
            $output,
            $result
        );
        assertThat($result, is(0));
    }

    function setUp()
    {
        self::$_wireMock->reset();
    }

    function testRequestWithUrlAndStringBodyCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBody('Here is some body text')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    function testRequestUrlAndBodyFileCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBodyFile('someFile.html')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    function testMappingsCanBeReset()
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
}

function assertThatTheOnlyMappingPresentIs(StubMapping $stubMapping)
{
    $mappings = getMappings();
    assertThat($mappings, is(arrayWithSize(1)));
    assertThat($mappings[0], is($stubMapping->toArray()));
}

function assertThatThereAreNoMappings()
{
    $mappings = getMappings();
    assertThat($mappings, is(emptyArray()));
}

function getMappings()
{
    $adminJson = file_get_contents('http://localhost:8080/__admin');
    $admin = json_decode($adminJson, true);
    return $admin['mappings'];
}