<?php

namespace WireMock\Matching;

use WireMock\HamcrestTestCase;

class UrlMatchingStrategyTest extends HamcrestTestCase
{
    public function testMatchingTypeAndMatchingValueAreAvailable()
    {
        // given
        $matchingType = 'url';
        $matchingValue = '/some/thing';
        $urlMatchingStrategy = new UrlMatchingStrategy($matchingType, $matchingValue);

        // then
        assertThat($urlMatchingStrategy->getMatchingType(), is($matchingType));
        assertThat($urlMatchingStrategy->getMatchingValue(), is($matchingValue));
    }
}
