<?php

namespace WireMock\Matching;

use Phake;
use WireMock\Client\WireMock;
use WireMock\HamcrestTestCase;

class RequestPatternTest extends HamcrestTestCase
{
    public function testMethodAndMatchingStategyAreAvailable()
    {
        // given
        $method = 'GET';
        $urlMatchingStrategy = new UrlMatchingStrategy('url', '/some/url');
        $requestPattern = new RequestPattern($method, $urlMatchingStrategy);

        // then
        assertThat($requestPattern->getMethod(), is($method));
        assertThat($requestPattern->getUrlMatchingStrategy(), is($urlMatchingStrategy));
    }

    public function testRequestHeaderMatchersAreAvailable()
    {
        // given
        $headers = array('aHeader' => WireMock::equalTo('aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            new UrlMatchingStrategy('url', '/some/url'),
            $headers
        );

        // then
        assertThat($requestPattern->getHeaders(), is($headers));
    }

    public function testRequestCookieMatchersAreAvailable()
    {
        // given
        $cookies = array('aCookie' => WireMock::equalTo('aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            new UrlMatchingStrategy('url', '/some/url'),
            null,
            $cookies
        );

        // then
        assertThat($requestPattern->getCookies(), is($cookies));
    }

    public function testRequestBodyMatchersAreAvailable()
    {
        // given
        $bodyPatterns = array(WireMock::equalTo('aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            new UrlMatchingStrategy('url', '/some/url'),
            null,
            null,
            $bodyPatterns
        );

        // then
        assertThat($requestPattern->getBodyPatterns(), is($bodyPatterns));
    }

    public function testQueryParamMatchersAreAvailable()
    {
        // given
        $queryParams = array('aParam' => WireMock::equalTo('aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            new UrlMatchingStrategy('url', '/some/url'),
            null,
            null,
            null,
            null,
            $queryParams
        );

        // then
        assertThat($requestPattern->getQueryParameters(), is($queryParams));
    }
}
