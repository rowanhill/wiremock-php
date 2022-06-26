<?php

namespace WireMock\Serde\TestClasses;

class NamedProperty
{
    /**
     * @var string
     * @serde-name serializedName
     */
    private $originalName = 'value';
}