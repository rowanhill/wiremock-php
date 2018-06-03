<?php

namespace WireMock\Matching;

// Dummy use of UrlMatchingStrategy, to force loader to bring the file in, so this class can be mocked (useful for when
// these tests are run in isolation)
new UrlMatchingStrategy('foo', 'bar');

class RequestPatternTest extends \PHPUnit_Framework_TestCase
{
    public function testMethodAndMatchingTypeAndMatchingValueAreAvailableAsArray()
    {
        // given
        $method = 'GET';
        $matchingType = 'url';
        $matchingValue = '/some/url';
        /** @var UrlMatchingStrategy $mockUrlMatchingStrategy */
        $mockUrlMatchingStrategy = mock('Wiremock\Matching\UrlMatchingStrategy');
        when($mockUrlMatchingStrategy->toArray())->return(array($matchingType => $matchingValue));
        $requestPattern = new RequestPattern($method, $mockUrlMatchingStrategy);

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('method', $method));
        assertThat($requestPatternArray, hasEntry($matchingType, $matchingValue));
    }

    public function testRequestHeaderMatchersAreAvailableInArray()
    {
        // given
        /** @var UrlMatchingStrategy $mockUrlMatchingStrategy */
        $mockUrlMatchingStrategy = mock('Wiremock\Matching\UrlMatchingStrategy');
        when($mockUrlMatchingStrategy->toArray())->return(array('url' => '/some/url'));
        $headers = array('aHeader' => array('equalTo' => 'aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            $headers
        );

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('headers', $headers));
    }

    public function testRequestCookieMatchersAreAvailableInArray()
    {
        // given
        /** @var UrlMatchingStrategy $mockUrlMatchingStrategy */
        $mockUrlMatchingStrategy = mock('Wiremock\Matching\UrlMatchingStrategy');
        when($mockUrlMatchingStrategy->toArray())->return(array('url' => '/some/url'));
        $cookies = array('aCookie' => array('equalTo' => 'aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            null,
            $cookies
        );

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('cookies', $cookies));
    }

    public function testRequestBodyMatchersAreAvailableInArray()
    {
        // given
        /** @var UrlMatchingStrategy $mockUrlMatchingStrategy */
        $mockUrlMatchingStrategy = mock('Wiremock\Matching\UrlMatchingStrategy');
        when($mockUrlMatchingStrategy->toArray())->return(array('url' => '/some/url'));
        $bodyPatterns = array(array('equalTo' => 'aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
            null,
            null,
            $bodyPatterns
        );

        // when
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('bodyPatterns', $bodyPatterns));
    }

    public function testQueryParamMatchersAreAvailableInArray()
    {
        // given
        /** @var UrlMatchingStrategy $mockUrlMatchingStrategy */
        $mockUrlMatchingStrategy = mock('Wiremock\Matching\UrlMatchingStrategy');
        when($mockUrlMatchingStrategy->toArray())->return(array('url' => '/some/url'));
        $queryParams = array('aParam' => array('equalTo' => 'aValue'));
        $requestPattern = new RequestPattern(
            'GET',
            $mockUrlMatchingStrategy,
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
