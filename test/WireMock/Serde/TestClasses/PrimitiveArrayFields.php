<?php

namespace WireMock\Serde\TestClasses;

class PrimitiveArrayFields
{
    /** @var array */
    private $untypedArray;
    /** @var int[] */
    private $intArray;
    /** @var array<string, int> */
    private $intByString;

    public function __construct(array $untypedArray, array $intArray, array $intByString)
    {
        $this->untypedArray = $untypedArray;
        $this->intArray = $intArray;
        $this->intByString = $intByString;
    }


}