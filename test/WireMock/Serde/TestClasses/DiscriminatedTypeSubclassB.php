<?php

namespace WireMock\Serde\TestClasses;

class DiscriminatedTypeSubclassB extends DiscriminatedTypeParent
{
    /** @var array */
    private $subclassBProp;

    public function __construct(array $subclassBProp)
    {
        parent::__construct('b');
        $this->subclassBProp = $subclassBProp;
    }
}