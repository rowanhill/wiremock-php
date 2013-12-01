<?php

namespace WireMock\Client;

class ValueMatchingStrategyTest extends \PHPUnit_Framework_TestCase
{
    function testMatchingStrategyAndMatchedValueAreInArray()
    {
        // given
        $matchingType = 'equalTo';
        $matchingValue = '/some/thing';
        $valueMatchingStrategy = new ValueMatchingStrategy($matchingType, $matchingValue);

        // when
        $requestPatternArray = $valueMatchingStrategy->toArray();

        // then
        assertThat($requestPatternArray, hasEntry($matchingType, $matchingValue));
    }
}