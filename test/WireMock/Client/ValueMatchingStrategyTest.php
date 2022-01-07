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

        // then
        assertThat($valueMatchingStrategy->getMatchingType(), equalTo($matchingType));
        assertThat($valueMatchingStrategy->getMatchingValue(), equalTo($matchingValue));
    }
}
