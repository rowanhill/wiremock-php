<?php

namespace WireMock\Matching;

use Phake;
use WireMock\Client\WireMock;
use WireMock\HamcrestTestCase;

class RequestPatternTest extends HamcrestTestCase
{
    public function testMethodAndMatchingTypeAndMatchingValueAreAvailableAsArray()
    {
        // given
        $method = 'GET';
        $matchingType = 'url';
        $matchingValue = '/some/url';
        $mockUrlMatchingStrategy = Phake::mock(UrlMatchingStrategy::class);
        Phake::when($mockUrlMatchingStrategy)->toArray()->thenReturn(array($matchingType => $matchingValue));
        $requestPattern = new RequestPattern($method, $mockUrlMatchingStrategy, null);

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('method', $method));
        assertThat($requestPatternArray, hasEntry($matchingType, $matchingValue));
    }

    public function testRequestHeaderMatchersAreAvailableInArray()
    {
        // given
        $mockUrlMatchingStrategy = Phake::mock(UrlMatchingStrategy::class);
        Phake::when($mockUrlMatchingStrategy)->toArray()->thenReturn(array('url' => '/some/url'));
        $headers = array('aHeader' => WireMock::equalTo('aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            $headers,
            null
        );
        $headersArray = array_map(function($h) { return $h->toArray(); }, $headers);

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('headers', $headersArray));
    }

    public function testRequestCookieMatchersAreAvailableInArray()
    {
        // given
        $mockUrlMatchingStrategy = Phake::mock(UrlMatchingStrategy::class);
        Phake::when($mockUrlMatchingStrategy)->toArray()->thenReturn(array('url' => '/some/url'));
        $cookies = array('aCookie' => WireMock::equalTo('aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            null,
            $cookies,
            null
        );
        $cookiesArray = array_map(function($c) { return $c->toArray(); }, $cookies);

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('cookies', $cookiesArray));
    }

    public function testRequestBodyMatchersAreAvailableInArray()
    {
        // given
        $mockUrlMatchingStrategy = Phake::mock(UrlMatchingStrategy::class);
        Phake::when($mockUrlMatchingStrategy)->toArray()->thenReturn(array('url' => '/some/url'));
        $bodyPatterns = array(WireMock::equalTo('aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            null,
            null,
            $bodyPatterns,
            null
        );
        $bodyPatternsArray = array_map(function($bp) { return $bp->toArray(); }, $bodyPatterns);

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('bodyPatterns', $bodyPatternsArray));
    }

    public function testQueryParamMatchersAreAvailableInArray()
    {
        // given
        $mockUrlMatchingStrategy = Phake::mock(UrlMatchingStrategy::class);
        Phake::when($mockUrlMatchingStrategy)->toArray()->thenReturn(array('url' => '/some/url'));
        $queryParams = array('aParam' => WireMock::equalTo('aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            null,
            null,
            null,
            null,
            $queryParams
        );
        $queryParamsArray = array_map(function($qp) { return $qp->toArray(); }, $queryParams);

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('queryParameters', $queryParamsArray));
    }
}
