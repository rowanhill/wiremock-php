<?php

namespace WireMock\Serde\TestClasses;

class DiscriminatedTypeSubclassA extends DiscriminatedTypeParent
{
    /** @var int */
    private $subclassAProp;

    public function __construct(int $subclassAProp)
    {
        parent::__construct('a');
        $this->subclassAProp = $subclassAProp;
    }
}