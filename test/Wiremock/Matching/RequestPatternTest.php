<?php

namespace WireMock\Matching;

class RequestPatternTest extends \PHPUnit_Framework_TestCase
{
    function testMethodAndMatchingTypeAndMatchingValueAreAvailableAsArray()
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

    function testRequestHeaderMatchersAreAvailableInArray()
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
}