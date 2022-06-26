<?php

namespace WireMock\Serde\TestClasses;

class UnionTypeFields
{
    /** @var string|int|bool */
    private $primitivesUnion;
    /** @var int[]|int */
    private $arrayUnion;
    /** @var float|DefaultedField */
    private $classUnion;
    /** @var (string|int)[] */
    private $arrayOfUnion;

    /**
     * @param bool|int|string $primitivesUnion
     * @param int|int[] $arrayUnion
     * @param float|DefaultedField $classUnion
     * @param (string|int)[] $arrayOfUnion
     */
    public function __construct($primitivesUnion, $arrayUnion, $classUnion, $arrayOfUnion)
    {
        $this->primitivesUnion = $primitivesUnion;
        $this->arrayUnion = $arrayUnion;
        $this->classUnion = $classUnion;
        $this->arrayOfUnion = $arrayOfUnion;
    }

}