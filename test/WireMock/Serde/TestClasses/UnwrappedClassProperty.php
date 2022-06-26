<?php

namespace WireMock\Serde\TestClasses;

class UnwrappedClassProperty
{
    /** @var int */
    private $topLevel;
    /**
     * @var FieldOnlyPrimitives
     * @serde-unwrapped
     */
    private $unwrappedClass;

    public function __construct(int $topLevel, FieldOnlyPrimitives $unwrappedClass)
    {
        $this->topLevel = $topLevel;
        $this->unwrappedClass = $unwrappedClass;
    }
}