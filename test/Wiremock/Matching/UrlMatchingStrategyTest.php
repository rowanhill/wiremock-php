<?php

namespace WireMock\Matching;

class UrlMatchingStrategyTest extends \PHPUnit_Framework_TestCase
{
    function testMatchingTypeAndMatchingValueAreAvailableAsArray()
    {
        // given
        $matchingType = 'url';
        $matchingValue = '/some/thing';
        $urlMatchingStrategy = new UrlMatchingStrategy($matchingType, $matchingValue);

        // when
        $requestPatternArray = $urlMatchingStrategy->toArray();

        // then
        assertThat($requestPatternArray, hasEntry($matchingType, $matchingValue));
    }
}