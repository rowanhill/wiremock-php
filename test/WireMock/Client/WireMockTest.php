<?php

namespace WireMock\Client;

use WireMock\Matching\RequestPattern;
use WireMock\Stubbing\StubMapping;

class WireMockTest extends \PHPUnit_Framework_TestCase
{
    /** @var HttpWait */
    private $_mockHttpWait;
    /** @var Curl */
    private $_mockCurl;

    /** @var WireMock */
    private $_wireMock;

    public function setUp()
    {
        $this->_mockHttpWait = mock('WireMock\Client\HttpWait');
        $this->_mockCurl = mock('WireMock\Client\Curl');

        $this->_wireMock = new WireMock($this->_mockHttpWait, $this->_mockCurl);
    }

    public function testApiIsAliveIfServerReturns200()
    {
        // given
        when($this->_mockHttpWait->waitForServerToGive200('http://localhost:8080/__admin/'))->return(true);

        // when
        $isAlive = $this->_wireMock->isAlive();

        // then
        assertThat($isAlive, is(true));
    }

    public function testApiIsNotAliveIfServerDoesNotReturn200()
    {
        // given
        when($this->_mockHttpWait->waitForServerToGive200('http://localhost:8080/__admin'))->return(false);

        // when
        $isAlive = $this->_wireMock->isAlive();

        // then
        assertThat($isAlive, is(false));
    }

    public function testStubbingPostsJsonSerialisedObjectToWireMock()
    {
        // given
        /** @var StubMapping $mockStubMapping */
        $mockStubMapping = mock('WireMock\Stubbing\StubMapping');
        $stubMappingArray = array('some' => 'json');
        when($mockStubMapping->toArray())->return($stubMappingArray);
        /** @var MappingBuilder $mockMappingBuilder */
        $mockMappingBuilder = mock('WireMock\Client\MappingBuilder');
        when($mockMappingBuilder->build())->return($mockStubMapping);

        // when
        $this->_wireMock->stubFor($mockMappingBuilder);

        // then
        verify($this->_mockCurl)->post('http://localhost:8080/__admin/mappings/new', $stubMappingArray);
    }

    public function testVerifyingPostsJsonSerialisedObjectToWireMock()
    {
        // given
        /** @var RequestPattern $mockRequestPattern */
        $mockRequestPattern = mock('WireMock\Matching\RequestPattern');
        $requestPatternArray = array('some' => 'json');
        when($mockRequestPattern->toArray())->return($requestPatternArray);
        /** @var RequestPatternBuilder $mockRequestPatternBuilder */
        $mockRequestPatternBuilder = mock('WireMock\Client\RequestPatternBuilder');
        when($mockRequestPatternBuilder->build())->return($mockRequestPattern);
        when($this->_mockCurl->post('http://localhost:8080/__admin/requests/count', $requestPatternArray))
            ->return('{"count":1}');

        // when
        $this->_wireMock->verify($mockRequestPatternBuilder);

        // then
        verify($this->_mockCurl)->post('http://localhost:8080/__admin/requests/count', $requestPatternArray);
    }
}
