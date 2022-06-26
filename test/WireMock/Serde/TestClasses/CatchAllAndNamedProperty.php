<?php

namespace WireMock\Serde\TestClasses;

class CatchAllAndNamedProperty
{
    /**
     * @var array<string, string>
     * @serde-catch-all
     * @serde-name ignoredSerializedName
     */
    private $original;

    public function __construct(array $original)
    {
        $this->original = $original;
    }
}