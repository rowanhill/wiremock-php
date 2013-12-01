<?php

namespace WireMock\Client;

require_once 'WireMockIntegrationTest.php';

use WireMock\Client\WireMockIntegrationTest;

class VerificationIntegrationTest extends WireMockIntegrationTest
{
    function testCanVerifySimpleGetToUrl()
    {
        // given
        @file_get_contents('http://localhost:8080/some/url');

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    function testVerifyingUnrequestedUrlThrowsException()
    {
        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    function testCanVerifyRequestHasHeader()
    {
        // given
        $this->_getRequestWithHeaders('http://localhost:8080/some/url', array('Cookie: foo=bar'));

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withHeader('Cookie', WireMock::equalTo('foo=bar')));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    function testVerifyingRequestWithMissingHeaderThrowsException()
    {
        // given
        @file_get_contents('http://localhost:8080/some/url');

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withHeader('Cookie', WireMock::equalTo('foo=bar')));
    }

    function testCanVerifyRequestDoesNotHaveHeader()
    {
        // given
        @file_get_contents('http://localhost:8080/some/url');

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withoutHeader('Cookie'));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    function testVerifyingAbsenceOfPresentHeaderThrowsException()
    {
        // given
        $this->_getRequestWithHeaders('http://localhost:8080/some/url', array('Cookie: foo=bar'));

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withoutHeader('Cookie'));
    }

    function testCanVerifyRequestHasBody()
    {
        // given
        $this->_postRequestWithBody('http://localhost:8080/some/url', 'Some Body');

        // when
        self::$_wireMock->verify(WireMock::postRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::equalTo('Some Body')));
    }

    function testFindingAllRequestsReturnsMatchingRequestDetails()
    {
        // given
        $this->_getRequestWithHeaders('http://localhost:8080/some/url', array('Cookie: foo=bar'));

        // when
        $requests = self::$_wireMock->findAll(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));

        // then
        assertThat($requests, is(arrayWithSize(1)));
        /** @var LoggedRequest $request */
        $request = current($requests);
        assertThat($request->getUrl(), is('/some/url'));
    }

    private function _getRequestWithHeaders($url, array $headers)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_exec($ch);
        curl_close($ch);
    }

    private function _postRequestWithBody($url, $body)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_exec($ch);
        curl_close($ch);
    }
}