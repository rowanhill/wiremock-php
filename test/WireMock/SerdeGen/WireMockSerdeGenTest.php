<?php

namespace WireMock\SerdeGen;

use ReflectionException;
use WireMock\HamcrestTestCase;
use WireMock\Serde\SerializationException;
use WireMock\Serde\Type\SerdeTypeLookup;

class WireMockSerdeGenTest extends HamcrestTestCase
{
    /**
     * @throws ReflectionException
     * @throws SerializationException
     */
    public function testGeneratingLookup()
    {
        $result = WireMockSerdeGen::generateSerializedWiremockSerdeLookup();
        $lookup = unserialize($result);

        assertThat($lookup, self::isInstanceOf(SerdeTypeLookup::class));
    }
}
