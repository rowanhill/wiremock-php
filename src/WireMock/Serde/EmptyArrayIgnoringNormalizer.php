<?php

namespace WireMock\Serde;

use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

class EmptyArrayIgnoringNormalizer extends PropertyNormalizer
{
    public function normalize($object, $format = null, array $context = [])
    {
        $dataArray = parent::normalize($object, $format, $context);

        return array_filter($dataArray, function($value) {
            return !is_array($value) || count($value) > 0;
        });
    }
}