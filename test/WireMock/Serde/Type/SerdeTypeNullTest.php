<?php

namespace WireMock\Serde\Type;

use WireMock\HamcrestTestCase;

class SerdeTypeNullTest extends HamcrestTestCase
{
    public function testDenormalizesNullToNull()
    {
        $serdeType = new SerdeTypeNull();

        $data = null;
        $denormalized = $serdeType->denormalize($data, []);

        assertThat($denormalized, is(null));
    }

    /** @dataProvider providerNonNullData */
    public function testDenormalizingNonNullThrows($data)
    {
        $this->expectExceptionMessage('Cannot denormalize');
        $serdeType = new SerdeTypeNull();
        $serdeType->denormalize($data, []);
    }

    public function providerNonNullData(): array
    {
        return [
            'int' => [123],
            'bool' => [true],
            'float' => [1.23],
            'string' => ['a string'],
            'array' => [[]],
            'object' => [$this]
        ];
    }
}