<?php

namespace WireMock\Serde\TestClasses;

class FieldOnlyPrimitives
{
    /** @var int */
    private $int;
    /** @var float */
    private $float;
    /** @var bool */
    private $bool;
    /** @var string */
    private $string;

    public static function create(int $int, float $float, bool $bool, string $string)
    {
        $result = new FieldOnlyPrimitives();
        $result->int = $int;
        $result->float = $float;
        $result->bool = $bool;
        $result->string = $string;
        return $result;
    }
}