<?php

namespace WireMock\Serde;

use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;

interface MappingProvider
{
    static function getDiscriminatorMapping(): ClassDiscriminatorMapping;
}