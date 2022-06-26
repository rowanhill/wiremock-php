<?php

namespace WireMock\Serde\TestClasses;

class UnwrappedPrimitiveProperty
{
    /**
     * @var string
     * @serde-unwrapped
     */
    private $unwrappedPrimitive;

    public function __construct(string $unwrappedPrimitive)
    {
        $this->unwrappedPrimitive = $unwrappedPrimitive;
    }
}