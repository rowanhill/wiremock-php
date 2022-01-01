<?php

namespace WireMock\Matching;

use WireMock\HamcrestTestCase;

class UrlMatchingStrategyTest extends HamcrestTestCase
{
    public function testMatchingTypeAndMatchingValueAreAvailableAsArray()
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
