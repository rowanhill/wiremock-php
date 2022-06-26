<?php

namespace WireMock\Serde\TestClasses;

class CatchAllAndNamedByProperty
{
    /**
     * @var array<string, string>
     * @serde-catch-all
     * @serde-named-by namer
     * @serde-possible-names possibleNames
     */
    private $prop;
    /** @var string */
    private $namer;

    private static function possibleNames(): array { return ['newName']; }
}