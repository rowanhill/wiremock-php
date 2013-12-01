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
}