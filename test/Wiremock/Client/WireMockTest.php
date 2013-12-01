<?php

namespace WireMock\Client;

use WireMock\Stubbing\StubMapping;

class WireMockTest extends \PHPUnit_Framework_TestCase
{
    /** @var HttpWait */
    private $_mockHttpWait;
    /** @var Curl */
    private $_mockCurl;

    /** @var WireMock */
    private $_wireMock;

    function setUp()
    {
        $this->_mockHttpWait = mock('WireMock\Client\HttpWait');
        $this->_mockCurl = mock('WireMock\Client\Curl');

        $this->_wireMock = new WireMock($this->_mockHttpWait, $this->_mockCurl);
    }

    function testApiIsAliveIfServerReturns200()
    {
        // given
        when($this->_mockHttpWait->waitForServerToGive200('http://localhost:8080/__admin/'))->return(true);

        // when
        $isAlive = $this->_wireMock->isAlive();

        // then
        assertThat($isAlive, is(true));
    }

    function testApiIsNotAliveIfServerDoesNotReturn200()
    {
        // given
        when($this->_mockHttpWait->waitForServerToGive200('http://localhost:8080/__admin'))->return(false);

        // when
        $isAlive = $this->_wireMock->isAlive();

        // then
        assertThat($isAlive, is(false));
    }

    function testStubbingPostsJsonSerialisedObjectToWireMock()
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
}