<?php

namespace WireMock\Serde\TestClasses;

class ClassTypeFields
{
    /** @var FieldOnlyPrimitives */
    private $primitiveTypes;

    public function __construct(FieldOnlyPrimitives $primitiveTypes)
    {
        $this->primitiveTypes = $primitiveTypes;
    }
}