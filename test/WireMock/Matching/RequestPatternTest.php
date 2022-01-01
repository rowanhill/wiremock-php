<?php

namespace WireMock\Matching;

use Phake;
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
        $headers = array('aHeader' => array('equalTo' => 'aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            $headers,
            null
        );

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('headers', $headers));
    }

    public function testRequestCookieMatchersAreAvailableInArray()
    {
        // given
        $mockUrlMatchingStrategy = Phake::mock(UrlMatchingStrategy::class);
        Phake::when($mockUrlMatchingStrategy)->toArray()->thenReturn(array('url' => '/some/url'));
        $cookies = array('aCookie' => array('equalTo' => 'aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            null,
            $cookies,
            null
        );

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('cookies', $cookies));
    }

    public function testRequestBodyMatchersAreAvailableInArray()
    {
        // given
        $mockUrlMatchingStrategy = Phake::mock(UrlMatchingStrategy::class);
        Phake::when($mockUrlMatchingStrategy)->toArray()->thenReturn(array('url' => '/some/url'));
        $bodyPatterns = array(array('equalTo' => 'aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            null,
            null,
            $bodyPatterns,
            null
        );

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('bodyPatterns', $bodyPatterns));
    }

    public function testQueryParamMatchersAreAvailableInArray()
    {
        // given
        $mockUrlMatchingStrategy = Phake::mock(UrlMatchingStrategy::class);
        Phake::when($mockUrlMatchingStrategy)->toArray()->thenReturn(array('url' => '/some/url'));
        $queryParams = array('aParam' => array('equalTo' => 'aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            null,
            null,
            null,
            null,
            $queryParams
        );

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('queryParameters', $queryParams));
    }
}
