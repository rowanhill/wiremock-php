<?php

namespace WireMock\Serde;

class ObjectToPopulateResult
{
    public $object;
    public $normalisedArray;

    /**
     * @param ?object $object
     * @param array $normalisedArray
     */
    public function __construct(?object $object, array $normalisedArray)
    {
        $this->object = $object;
        $this->normalisedArray = $normalisedArray;
    }

}