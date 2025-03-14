<?php

namespace WireMock\Integration;

use WireMock\Client\RequestPatternBuilder;
use WireMock\Client\VerificationException;
use WireMock\Client\WireMock;

class VerificationIntegrationTest extends WireMockIntegrationTest
{
    private $_verificationCount = 0;

    private function verify($requestPatternBuilderOrCount, ?RequestPatternBuilder $requestPatternBuilder = null)
    {
        self::$_wireMock->verify($requestPatternBuilderOrCount, $requestPatternBuilder);
        $this->_verificationCount++;
    }

    public function runBare(): void
    {
        $this->_verificationCount = 0;

        try {
            parent::runBare();
        } finally {
            $this->addToAssertionCount($this->_verificationCount);
        }
    }

    public function testCanVerifySimpleGetToUrl()
    {
        // given
        $this->_testClient->get('/some/url');

        // when
        $this->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    public function testVerifyingUnrequestedUrlThrowsException()
    {
        // then
        $this->expectException(VerificationException::class);

        // when
        $this->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    public function testCanVerifyRequestHasHeader()
    {
        // given
        $this->_testClient->get('/some/url', array('Cookie: foo=bar'));

        // when
        $this->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withHeader('Cookie', WireMock::equalTo('foo=bar')));
    }

    public function testVerifyingRequestWithMissingHeaderThrowsException()
    {
        // then
        $this->expectException(VerificationException::class);

        // given
        $this->_testClient->get('/some/url');

        // when
        $this->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withHeader('Cookie', WireMock::equalTo('foo=bar')));
    }

    public function testCanVerifyRequestDoesNotHaveHeader()
    {
        // given
        $this->_testClient->get('/some/url');

        // when
        $this->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withoutHeader('Cookie'));
    }

    public function testVerifyingAbsenceOfPresentHeaderThrowsException()
    {
        // then
        $this->expectException(VerificationException::class);

        // given
        $this->_testClient->get('/some/url', array('Cookie: foo=bar'));

        // when
        $this->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withoutHeader('Cookie'));
    }

    public function testCanVerifyRequestWithQuery()
    {
        // given
        $this->_testClient->get('/some/url?foo=bar');

        // when
        $this->verify(WireMock::getRequestedFor(WireMock::urlMatching('/some/url.*'))
            ->withQueryParam('foo', WireMock::equalTo('bar')));
    }

    public function testVerifyingWiremockUrlEqualThrowsException()
    {
        // then
        $this->expectException(VerificationException::class);

        // given
        $this->_testClient->get('/some/url?foo=bar');

        // when
        $this->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withQueryParam('foo', WireMock::equalTo('bar')));
    }

    public function testVerifyingRequestWithMissingQueryThrowsException()
    {
        // then
        $this->expectException(VerificationException::class);

        // given
        $this->_testClient->get('/some/url');

        // when
        $this->verify(WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withQueryParam('foo', WireMock::equalTo('bar')));
    }

    public function testCanVerifyRequestHasBody()
    {
        // given
        $this->_testClient->post('/some/url', 'Some Body');

        // when
        $this->verify(WireMock::postRequestedFor(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::equalTo('Some Body')));
    }

    public function testCanVerifyASpecificNumberOfRequestsOccurred()
    {
        // given
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');

        // when
        $this->verify(3, WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    public function testCanVerifyAsComparisonOperator()
    {
        // given
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');

        // when
        $this->verify(WireMock::moreThan(2), WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    public function testVerifyingZeroRequestsWhenSomeRequestsWereMadeThrowsException()
    {
        // then
        $this->expectException(VerificationException::class);

        // given
        $this->_testClient->get('/some/url');

        // when
        $this->verify(0, WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }

    public function testVerifyingWrongNumberOfRequestsThrowsException()
    {
        // then
        $this->expectException(VerificationException::class);

        // given
        $this->_testClient->get('/some/url');
        $this->_testClient->get('/some/url');

        // when
        $this->verify(3, WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url')));
    }
}
