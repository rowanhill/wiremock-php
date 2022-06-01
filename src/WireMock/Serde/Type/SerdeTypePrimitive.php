<?php

namespace WireMock\Serde\Type;

use WireMock\Serde\SerializationException;
use WireMock\Serde\Serializer;

class SerdeTypePrimitive extends SerdeTypeSingle
{
    function canDenormalize($data): bool
    {
        $dataType = gettype($data);

        switch ($this->typeString) {
            case 'bool':
            case 'boolean':
                return $dataType === 'boolean';

            case 'int':
            case 'integer':
                return $dataType === 'integer';

            case 'float':
            case 'double':
                return $dataType === 'double';

            case 'string':
                return $dataType === 'string';

            default:
                return false;
        }
    }

    function denormalize(&$data, Serializer $serializer, array $path)
    {
        if (!$this->canDenormalize($data)) {
            $dataType = gettype($data);
            $targetType = $this->displayName();
            throw new SerializationException("Cannot deserialize data of type $dataType to $targetType");
        }
        return $data;
    }
}