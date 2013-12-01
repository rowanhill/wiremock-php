<?php

use WireMock\Client\WireMock;

class StubbingIntegrationTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        exec('cd ../wiremock && `java -jar wiremock-1.33-standalone.jar &> wiremock.log &`');
        $wiremock = WireMock::create();
        assertThat($wiremock->isAlive(), is(true));
    }

    function tearDown()
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

    function testRequestWithUrlAndStringBodyCanBeStubbed()
    {
        // given
        $body = 'Here is some body text';
        $wiremock = WireMock::create();
        $wiremock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBody($body)));

        // when
        $result = file_get_contents('http://localhost:8080/some/url');

        // then
        assertThat($result, is($body));
    }

    function testRequestUrlAndBodyFileCanBeStubbed()
    {
        // given
        $wiremock = WireMock::create();
        $wiremock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBodyFile('someFile.html')));

        // when
        $result = file_get_contents('http://localhost:8080/some/url');

        // then
        assertThat($result, is('<h1>Some File</h1>'));
    }
}