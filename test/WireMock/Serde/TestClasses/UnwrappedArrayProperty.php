<?php

namespace WireMock\Serde\TestClasses;

class UnwrappedArrayProperty
{
    /**
     * @var string[]
     * @serde-unwrapped
     */
    private $unwrappedArray;

    public function __construct(array $unwrappedArray)
    {
        $this->unwrappedArray = $unwrappedArray;
    }
}