<?php

namespace WireMock\Serde\TestClasses;

class UnwrappedAndNamedProperty
{
    /**
     * @var FieldOnlyPrimitives
     * @serde-unwrapped
     * @serde-name ignoredSerializedName
     */
    private $original;

    public function __construct(FieldOnlyPrimitives $original)
    {
        $this->original = $original;
    }
}