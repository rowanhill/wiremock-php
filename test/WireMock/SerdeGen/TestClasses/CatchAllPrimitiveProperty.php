<?php

namespace WireMock\SerdeGen\TestClasses;

class CatchAllPrimitiveProperty
{
    /**
     * @var string
     * @serde-catch-all
     */
    private $catchAllPrimitive;

    public function __construct(string $catchAllPrimitive)
    {
        $this->catchAllPrimitive = $catchAllPrimitive;
    }
}