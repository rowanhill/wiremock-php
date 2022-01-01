<?php

namespace WireMock;

use Hamcrest\MatcherAssert;
use Hamcrest\Util;
use PHPUnit\Framework\TestCase;

class HamcrestTestCase extends TestCase
{
    public function runBare()
    {
        Util::registerGlobalFunctions();
        MatcherAssert::resetCount();

        try {
            parent::runBare();
        } finally {
            $this->addToAssertionCount(MatcherAssert::getCount());
        }
    }
}