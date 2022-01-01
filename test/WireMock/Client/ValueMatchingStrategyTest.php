<?php

namespace WireMock\Client;

use WireMock\HamcrestTestCase;

class ValueMatchingStrategyTest extends HamcrestTestCase
{
    public function testMatchingStrategyAndMatchedValueAreInArray()
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
