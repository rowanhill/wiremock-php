<?php

namespace WireMock\Serde;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PrivatePropertyNameConverter implements NameConverterInterface
{
    public function normalize($propertyName)
    {
        if (substr($propertyName, 0, 1) == '_') {
            return substr($propertyName, 1);
        } else {
            return $propertyName;
        }
    }

    public function denormalize($propertyName)
    {
        return '_' . $propertyName;
    }
}