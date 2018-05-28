<?php

namespace WireMock\Integration;

use WireMock\Client\LoggedRequest;
use WireMock\Client\WireMock;

require_once 'WireMockIntegrationTest.php';

class VerificationIntegrationTest extends WireMockIntegrationTest
{
    public function testCanVerifySimpleGetToUrl()
    {
        // given
        $this->_testClient->get('/some/url');

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    public function testVerifyingUnrequestedUrlThrowsException()
    {
        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    public function testCanVerifyRequestHasHeader()
    {
        // given
        $this->_testClient->get('/some/url', array('Cookie: foo=bar'));

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withHeader('Cookie', WireMock::equalTo('foo=bar')));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    public function testVerifyingRequestWithMissingHeaderThrowsException()
    {
        // given
        $this->_testClient->get('/some/url');

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withHeader('Cookie', WireMock::equalTo('foo=bar')));
    }

    public function testCanVerifyRequestDoesNotHaveHeader()
    {
        // given
        $this->_testClient->get('/some/url');

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withoutHeader('Cookie'));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    public function testVerifyingAbsenceOfPresentHeaderThrowsException()
    {
        // given
        $this->_testClient->get('/some/url', array('Cookie: foo=bar'));

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withoutHeader('Cookie'));
    }

    public function testCanVerifyRequestWithQuery()
    {
        // given
        $this->_testClient->get('/some/url?foo=bar');

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlMatching('/some/url.*'))
            ->withQueryParameter('foo', WireMock::equalTo('bar')));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    public function testVerifyingWiremockUrlEqualThrowsException()
    {
        // given
        $this->_testClient->get('/some/url?foo=bar');

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withQueryParameter('foo', WireMock::equalTo('bar')));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    public function testVerifyingRequestWithMissingQueryThrowsException()
    {
        // given
        $this->_testClient->get('/some/url');

        // when
        self::$_wireMock->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withQueryParameter('foo', WireMock::equalTo('bar')));
    }

    public function testCanVerifyRequestHasBody()
    {
        // given
        $this->_testClient->post('/some/url', 'Some Body');

        // when
        self::$_wireMock->verify(WireMock::postRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::equalTo('Some Body')));
    }

    public function testCanVerifyASpecificNumberOfRequestsOccurred()
    {
        // given
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');

        // when
        self::$_wireMock->verify(3, WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    public function testCanVerifyAsComparisonOperator()
    {
        // given
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');

        // when
        self::$_wireMock->verify(WireMock::moreThan(2), WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    public function testVerifyingZeroRequestsWhenSomeRequestsWereMadeThrowsException()
    {
        // given
        $this->_testClient->get('/some/url');

        // when
        self::$_wireMock->verify(0, WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    /**
     * @expectedException \WireMock\Client\VerificationException
     */
    public function testVerifyingWrongNumberOfRequestsThrowsException()
    {
        // given
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');

        // when
        self::$_wireMock->verify(3, WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    public function testFindingAllRequestsReturnsMatchingRequestDetails()
    {
        // given
        $this->_testClient->get('/some/url', array('Cookie: foo=bar'));

        // when
        $requests = self::$_wireMock->findAll(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));

        // then
        assertThat($requests, is(arrayWithSize(1)));
        /** @var LoggedRequest $request */
        $request = current($requests);
        assertThat($request->getUrl(), is('/some/url'));
    }
}
