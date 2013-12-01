<?php

use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;
use WireMock\Client\WireMock;
use WireMock\Matching\UrlMatchingStrategy;
use WireMock\Stubbing\StubMapping;

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
        $urlMatcher = new UrlMatchingStrategy('url', '/some/url');
        $requestMatcher = new RequestPattern('GET', $urlMatcher);
        $responseDefinition = new ResponseDefinition();
        $responseDefinition->setBody('Here is some body text');
        $stubMapping = new StubMapping($requestMatcher, $responseDefinition);

        // when
        WireMock::create()->stubFor($stubMapping);
        $result = file_get_contents('http://localhost:8080/some/url');

        // then
        assertThat($result, is('Here is some body text'));
    }
}