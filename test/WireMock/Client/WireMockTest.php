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
        when($this->_mockCurl->post('http://localhost:8080/__admin/mappings', $stubMappingArray))
            ->return(json_encode(array('id' => 'some-long-guid')));

        // when
        $this->_wireMock->stubFor($mockMappingBuilder);

        // then
        verify($this->_mockCurl)->post('http://localhost:8080/__admin/mappings', $stubMappingArray);
    }

    public function testEditingStubsPutsJsonSerialisedObjectAtUrlWithIdToWireMock()
    {
        // given
        /** @var StubMapping $mockStubMapping */
        $mockStubMapping = mock('WireMock\Stubbing\StubMapping');
        when($mockStubMapping->getId())->return('some-long-guid');
        $stubMappingArray = array('some' => 'json');
        when($mockStubMapping->toArray())->return($stubMappingArray);
        /** @var MappingBuilder $mockMappingBuilder */
        $mockMappingBuilder = mock('WireMock\Client\MappingBuilder');
        when($mockMappingBuilder->build())->return($mockStubMapping);

        // when
        $this->_wireMock->editStub($mockMappingBuilder);

        // then
        verify($this->_mockCurl)->put('http://localhost:8080/__admin/mappings/some-long-guid', $stubMappingArray);
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    public function testEditingStubWithoutAnIdThrowsException()
    {
        // given
        /** @var StubMapping $mockStubMapping */
        $mockStubMapping = mock('WireMock\Stubbing\StubMapping');
        when($mockStubMapping->getId())->return(null);
        /** @var MappingBuilder $mockMappingBuilder */
        $mockMappingBuilder = mock('WireMock\Client\MappingBuilder');
        when($mockMappingBuilder->build())->return($mockStubMapping);

        // when
        $this->_wireMock->editStub($mockMappingBuilder);
    }

    public function testStubbingAddsReturnedIdToStubMappingObject()
    {
        // given
        /** @var StubMapping $mockStubMapping */
        $mockStubMapping = mock('WireMock\Stubbing\StubMapping');
        $stubMappingArray = array('some' => 'json');
        when($mockStubMapping->toArray())->return($stubMappingArray);
        /** @var MappingBuilder $mockMappingBuilder */
        $mockMappingBuilder = mock('WireMock\Client\MappingBuilder');
        when($mockMappingBuilder->build())->return($mockStubMapping);
        $id = 'some-long-guid';
        when($this->_mockCurl->post(anything(), $stubMappingArray))->return(json_encode(array('id' => $id)));

        // when
        $this->_wireMock->stubFor($mockMappingBuilder);

        // then
        verify($mockStubMapping)->setId($id);
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

    public function testGetWithUrlTreatedAsUrlEqualTo()
    {
        $this->_testConvenienceMethodBuildsUrlEqualTo('get', '/some/url');
    }

    public function testPostWithUrlTreatedAsUrlEqualTo()
    {
        $this->_testConvenienceMethodBuildsUrlEqualTo('post', '/some/url');
    }

    public function testDeleteWithUrlTreatedAsUrlEqualTo()
    {
        $this->_testConvenienceMethodBuildsUrlEqualTo('delete', '/some/url');
    }

    public function testPatchWithUrlTreatedAsUrlEqualTo()
    {
        $this->_testConvenienceMethodBuildsUrlEqualTo('patch', '/some/url');
    }

    public function testHeadWithUrlTreatedAsUrlEqualTo()
    {
        $this->_testConvenienceMethodBuildsUrlEqualTo('head', '/some/url');
    }

    public function testOptionsWithUrlTreatedAsUrlEqualTo()
    {
        $this->_testConvenienceMethodBuildsUrlEqualTo('options', '/some/url');
    }

    public function testTraceWithUrlTreatedAsUrlEqualTo()
    {
        $this->_testConvenienceMethodBuildsUrlEqualTo('trace', '/some/url');
    }

    public function testAnyWithUrlTreatedAsUrlEqualTo()
    {
        $this->_testConvenienceMethodBuildsUrlEqualTo('any', '/some/url');
    }

    public function testOkIsConvenienceForResponseWith200Status()
    {
        // when
        $responseDef = WireMock::ok()->build();

        // then
        assertThat($responseDef->getStatus(), is(200));
    }

    public function testOkWithBodyIsConvenienceForResponseWith200StatusWithBody()
    {
        // when
        $responseDef = WireMock::ok('body')->build();

        // then
        assertThat($responseDef->getStatus(), is(200));
        assertThat($responseDef->getBody(), is('body'));
    }

    public function testOkForContentTypeIsConvenienceForResponseWith200StatusWithContentType()
    {
        // when
        $responseDef = WireMock::okForContentType('custom', 'body')->build();

        // then
        assertThat($responseDef->getStatus(), is(200));
        assertThat($responseDef->getHeaders(), is(array('Content-Type' => 'custom')));
        assertThat($responseDef->getBody(), is('body'));
    }

    public function testOkJsonIsConvenienceForResponseWith200StatusWithJsonContentType()
    {
        // when
        $responseDef = WireMock::okJson('body')->build();

        // then
        assertThat($responseDef->getStatus(), is(200));
        assertThat($responseDef->getHeaders(), is(array('Content-Type' => 'application/json')));
        assertThat($responseDef->getBody(), is('body'));
    }

    public function testOkXmlIsConvenienceForResponseWith200StatusWithAppXmlContentType()
    {
        // when
        $responseDef = WireMock::okXml('body')->build();

        // then
        assertThat($responseDef->getStatus(), is(200));
        assertThat($responseDef->getHeaders(), is(array('Content-Type' => 'application/xml')));
        assertThat($responseDef->getBody(), is('body'));
    }

    public function testOkTextXmlIsConvenienceForResponseWith200StatusWithTextXmlContentType()
    {
        // when
        $responseDef = WireMock::okTextXml('body')->build();

        // then
        assertThat($responseDef->getStatus(), is(200));
        assertThat($responseDef->getHeaders(), is(array('Content-Type' => 'text/xml')));
        assertThat($responseDef->getBody(), is('body'));
    }

    public function testProxyToAllIsConvenienceMethodForProxyingAnyToUrl()
    {
        // when
        $mapping = WireMock::proxyAllTo('http://localhost')->build();

        // then
        assertThat($mapping->getRequest()->getMethod(), is('ANY'));
        assertThat($mapping->getRequest()->getUrlMatchingStrategy()->toArray(), is(array('urlPattern' => '.*')));
        assertThat($mapping->getResponse()->getProxyBaseUrl(), is('http://localhost'));
    }

    public function testCreatedIsConvenienceForResponseWith201Status()
    {
        // when
        $responseDef = WireMock::created()->build();

        // then
        assertThat($responseDef->getStatus(), is(201));
    }

    public function testNoContentIsConvenienceForResponseWith204Status()
    {
        // when
        $responseDef = WireMock::noContent()->build();

        // then
        assertThat($responseDef->getStatus(), is(204));
    }

    public function testPermanentRedirectIsConvenienceForResponseWith301StatusAndLocationHeader()
    {
        // when
        $responseDef = WireMock::permanentRedirect('foo')->build();

        // then
        assertThat($responseDef->getStatus(), is(301));
        assertThat($responseDef->getHeaders(), is(array('Location' => 'foo')));
    }

    public function testTemporaryRedirectIsConvenienceForResponseWith302StatusAndLocationHeader()
    {
        // when
        $responseDef = WireMock::temporaryRedirect('foo')->build();

        // then
        assertThat($responseDef->getStatus(), is(302));
        assertThat($responseDef->getHeaders(), is(array('Location' => 'foo')));
    }

    public function testSeeOtherIsConvenienceForResponseWith303StatusAndLocationHeader()
    {
        // when
        $responseDef = WireMock::seeOther('foo')->build();

        // then
        assertThat($responseDef->getStatus(), is(303));
        assertThat($responseDef->getHeaders(), is(array('Location' => 'foo')));
    }

    public function testBadRequestIsConvenienceForResponseWith400Status()
    {
        // when
        $responseDef = WireMock::badRequest()->build();

        // then
        assertThat($responseDef->getStatus(), is(400));
    }

    public function testBadRequestEntityIsConvenienceForResponseWith422Status()
    {
        // when
        $responseDef = WireMock::badRequestEntity()->build();

        // then
        assertThat($responseDef->getStatus(), is(422));
    }

    public function testUnauthorizedIsConvenienceForResponseWith401Status()
    {
        // when
        $responseDef = WireMock::unauthorized()->build();

        // then
        assertThat($responseDef->getStatus(), is(401));
    }

    public function testForbiddenIsConvenienceForResponseWith403Status()
    {
        // when
        $responseDef = WireMock::forbidden()->build();

        // then
        assertThat($responseDef->getStatus(), is(403));
    }

    public function testNotFoundIsConvenienceForResponseWith404Status()
    {
        // when
        $responseDef = WireMock::notFound()->build();

        // then
        assertThat($responseDef->getStatus(), is(404));
    }

    public function testServerErrorIsConvenienceForResponseWith500Status()
    {
        // when
        $responseDef = WireMock::serverError()->build();

        // then
        assertThat($responseDef->getStatus(), is(500));
    }

    public function testServiceUnavailableIsConvenienceForResponseWith503Status()
    {
        // when
        $responseDef = WireMock::serviceUnavailable()->build();

        // then
        assertThat($responseDef->getStatus(), is(503));
    }

    public function testStatusIsConvenienceForResponseWithSpecifiedStatus()
    {
        // when
        $responseDef = WireMock::status(418)->build();

        // then
        assertThat($responseDef->getStatus(), is(418));
    }

    private function _testConvenienceMethodBuildsUrlEqualTo($method, $url)
    {
        // when
        $mappingBuilder = WireMock::$method($url)->willReturn(WireMock::aResponse());
        $mapping = $mappingBuilder->build();

        // then
        assertThat(
            $mapping->getRequest()->getUrlMatchingStrategy()->toArray(),
            equalTo(array('url' => $url))
        );
    }
}
