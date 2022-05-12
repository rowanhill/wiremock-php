<?php

namespace WireMock\Serde;

interface MappingProvider
{
    static function getDiscriminatorMapping(): ClassDiscriminator;
}