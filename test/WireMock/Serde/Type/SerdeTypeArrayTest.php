<?php


namespace WireMock\Serde\Type;

use WireMock\HamcrestTestCase;

class SerdeTypeArrayTest extends HamcrestTestCase
{
    /** @dataProvider providerNonArrayData */
    public function testDenormalizingNonArrayThrows($data)
    {
        $this->expectExceptionMessage('Cannot denormalize');
        $serdeType = new class extends SerdeTypeArray {
            function displayName(): string { return 'dummy'; }
            function denormalizeFromArray(array &$data, array $path): array { return ['dummy array']; }
        };
        $serdeType->denormalize($data, []);
    }

    public function providerNonArrayData(): array
    {
        return [
            'int' => [123],
            'bool' => [true],
            'float' => [1.23],
            'string' => ['a string'],
            'object' => [$this],
            'null' => [null],
        ];
    }
}