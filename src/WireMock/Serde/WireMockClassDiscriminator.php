<?php

namespace WireMock\Serde;

use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;

class WireMockClassDiscriminator implements ClassDiscriminatorResolverInterface
{
    public function getMappingForClass(string $class): ?ClassDiscriminatorMapping
    {
        if (is_subclass_of($class, MappingProvider::class)) {
            return forward_static_call(array($class, 'getDiscriminatorMapping'));
        }
        return null;
    }

    public function getMappingForMappedObject($object): ?ClassDiscriminatorMapping
    {
        return null;
    }

    public function getTypeForMappedObject($object): ?string
    {
        return null;
    }
}