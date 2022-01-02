<?php

namespace WireMock\Client;

use Phake;
use WireMock\HamcrestTestCase;
use WireMock\Matching\RequestPattern;
use WireMock\Stubbing\StubMapping;

class WireMockTest extends HamcrestTestCase
{
    /** @var HttpWait */
    private $_mockHttpWait;
    /** @var Curl */
    private $_mockCurl;

    /** @var WireMock */
    private $_wireMock;

    public function setUp(): void
    {
        $this->_mockHttpWait = Phake::mock(HttpWait::class);
        $this->_mockCurl = Phake::mock(Curl::class);

        $this->_wireMock = new WireMock($this->_mockHttpWait, $this->_mockCurl);
    }

    public function testApiIsAliveIfServerReturns200()
    {
        // given
        Phake::when($this->_mockHttpWait)->waitForServerToGive200('http://localhost:8080/__admin/', 10, true)
            ->thenReturn(true);

        // when
        $isAlive = $this->_wireMock->isAlive();

        // then
        assertThat($isAlive, is(true));
    }

    public function testApiIsNotAliveIfServerDoesNotReturn200()
    {
        // given
        Phake::when($this->_mockHttpWait)->waitForServerToGive200('http://localhost:8080/__admin')->thenReturn(false);

        // when
        $isAlive = $this->_wireMock->isAlive();

        // then
        assertThat($isAlive, is(false));
    }

    public function testStubbingPostsJsonSerialisedObjectToWireMock()
    {
        // given
        $mockStubMapping = Phake::mock(StubMapping::class);
        $stubMappingArray = array('some' => 'json');
        Phake::when($mockStubMapping)->toArray()->thenReturn($stubMappingArray);
        $mockMappingBuilder = Phake::mock(MappingBuilder::class);
        Phake::when($mockMappingBuilder)->build()->thenReturn($mockStubMapping);
        Phake::when($this->_mockCurl)->post('http://localhost:8080/__admin/mappings', $stubMappingArray)
            ->thenReturn(json_encode(array('id' => 'some-long-guid')));

        // when
        $this->_wireMock->stubFor($mockMappingBuilder);

        // then
        Phake::verify($this->_mockCurl)->post('http://localhost:8080/__admin/mappings', $stubMappingArray);
    }

    public function testEditingStubsPutsJsonSerialisedObjectAtUrlWithIdToWireMock()
    {
        // given
        $mockStubMapping = Phake::mock(StubMapping::class);
        Phake::when($mockStubMapping)->getId()->thenReturn('some-long-guid');
        $stubMappingArray = array('some' => 'json');
        Phake::when($mockStubMapping)->toArray()->thenReturn($stubMappingArray);
        $mockMappingBuilder = Phake::mock(MappingBuilder::class);
        Phake::when($mockMappingBuilder)->build()->thenReturn($mockStubMapping);

        // when
        $this->_wireMock->editStub($mockMappingBuilder);

        // then
        Phake::verify($this->_mockCurl)->put('http://localhost:8080/__admin/mappings/some-long-guid', $stubMappingArray);
    }

    public function testEditingStubWithoutAnIdThrowsException()
    {
        // then
        $this->expectException(VerificationException::class);

        // given
        $mockStubMapping = Phake::mock(StubMapping::class);
        Phake::when($mockStubMapping)->getId()->thenReturn(null);
        $mockMappingBuilder = Phake::mock(MappingBuilder::class);
        Phake::when($mockMappingBuilder)->build()->thenReturn($mockStubMapping);

        // when
        $this->_wireMock->editStub($mockMappingBuilder);
    }

    public function testStubbingAddsReturnedIdToStubMappingObject()
    {
        // given
        $mockStubMapping = Phake::mock(StubMapping::class);
        $stubMappingArray = array('some' => 'json');
        Phake::when($mockStubMapping)->toArray()->thenReturn($stubMappingArray);
        $mockMappingBuilder = Phake::mock(MappingBuilder::class);
        Phake::when($mockMappingBuilder)->build()->thenReturn($mockStubMapping);
        $id = 'some-long-guid';
        Phake::when($this->_mockCurl)->post(anything(), $stubMappingArray)->thenReturn(json_encode(array('id' => $id)));

        // when
        $this->_wireMock->stubFor($mockMappingBuilder);

        // then
        Phake::verify($mockStubMapping)->setId($id);
    }

    public function testVerifyingPostsJsonSerialisedObjectToWireMock()
    {
        // given
        $mockRequestPattern = Phake::mock(RequestPattern::class);
        $requestPatternArray = array('some' => 'json');
        Phake::when($mockRequestPattern)->toArray()->thenReturn($requestPatternArray);
        $mockRequestPatternBuilder = Phake::mock('WireMock\Client\RequestPatternBuilder');
        Phake::when($mockRequestPatternBuilder)->build()->thenReturn($mockRequestPattern);
        Phake::when($this->_mockCurl)->post('http://localhost:8080/__admin/requests/count', $requestPatternArray)
            ->thenReturn('{"count":1}');

        // when
        $this->_wireMock->verify($mockRequestPatternBuilder);

        // then
        Phake::verify($this->_mockCurl)->post('http://localhost:8080/__admin/requests/count', $requestPatternArray);
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

    public function testJsonResponseIsConvenienceForResponseWithStatusBodyAndJsonContentType()
    {
        // when
        $responseDef = WireMock::jsonResponse('body', 403)->build();

        // then
        assertThat($responseDef->getStatus(), is(403));
        assertThat($responseDef->getHeaders(), is(array('Content-Type' => 'application/json')));
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
