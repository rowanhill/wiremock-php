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
        $requestPattern = new RequestPattern('GET', $mockUrlMatchingStrategy);

        // when
        $headers = array('aHeader' => array('equalTo' => 'aValue'));
        $requestPattern->setHeaders($headers);
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
        $requestPattern = new RequestPattern('GET', $mockUrlMatchingStrategy);

        // when
        $cookies = array('aCookie' => array('equalTo' => 'aValue'));
        $requestPattern->setCookies($cookies);
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
        $requestPattern = new RequestPattern('GET', $mockUrlMatchingStrategy);

        // when
        $bodyPatterns = array(array('equalTo' => 'aValue'));
        $requestPattern->setBodyPatterns($bodyPatterns);
        $requestPatternArray = $requestPattern->toArray();

        // then
        assertThat($requestPatternArray, hasEntry('bodyPatterns', $bodyPatterns));
    }
}
