<?php

namespace WireMock\Serde\TestClasses;

class UnwrappedAndNamedByProperty
{
    /**
     * @var FieldOnlyPrimitives
     * @serde-unwrapped
     * @serde-named-by namer
     * @serde-possible-names possibleNames
     */
    private $prop;
    /** @var string */
    private $namer;

    private static function possibleNames(): array { return ['newName']; }
}