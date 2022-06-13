<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace WireMock\Serde\Type;

use WireMock\HamcrestTestCase;

class SerdeTypePrimitiveTest extends HamcrestTestCase
{
    private const EXAMPLE_DATA = [
        'bool' => false,
        'boolean' => false,
        'int' => 123,
        'integer' => 123,
        'float' => 1.23,
        'double' => 1.23,
        'string' => 'a string',
    ];
    private const EQUIVALENT_TYPES = [
        'bool' => 'boolean',
        'boolean' => 'bool',
        'int' => 'integer',
        'integer' => 'int',
        'float' => 'double',
        'double' => 'float'
    ];

    /** @dataProvider providerMatchedTypeAndData */
    public function testDenormalizingCorrectTypeDataIsANoOp(string $type, $data)
    {
        $serdeType = new SerdeTypePrimitive($type);
        $denormalized = $serdeType->denormalize($data, []);
        assertThat($denormalized, is($data));
    }

    /** @dataProvider providerMismatchedTypeAndData */
    public function testDenormalizingMismatchedTypeDataThrows(string $type, $data)
    {
        $this->expectExceptionMessage('Cannot deserialize data of type');
        $serdeType = new SerdeTypePrimitive($type);
        $serdeType->denormalize($data, []);
    }

    /** @dataProvider providerMatchedTypeAndData */
    public function testDenormalizingObjectDataThrows(string $type)
    {
        $this->expectExceptionMessage('Cannot deserialize data of type');
        $serdeType = new SerdeTypePrimitive($type);
        $serdeType->denormalize($this, []);
    }

    /** @dataProvider providerMatchedTypeAndData */
    public function testDenormalizingArrayDataThrows(string $type)
    {
        $this->expectExceptionMessage('Cannot deserialize data of type');
        $serdeType = new SerdeTypePrimitive($type);
        $data = [];
        $serdeType->denormalize($data, []);
    }

    /** @dataProvider providerMatchedTypeAndData */
    public function testDenormalizingNullDataThrows(string $type)
    {
        $this->expectExceptionMessage('Cannot deserialize data of type');
        $serdeType = new SerdeTypePrimitive($type);
        $data = null;
        $serdeType->denormalize($data, []);
    }

    public function providerMatchedTypeAndData(): array
    {
        $result = [];
        foreach (self::EXAMPLE_DATA as $type => $data) {
            $result[$type] = [$type, $data];
        }
        return $result;
    }

    public function providerMismatchedTypeAndData(): array
    {
        $result = [];
        foreach (self::EXAMPLE_DATA as $type => $ignoredData) {
            foreach (self::EXAMPLE_DATA as $otherType => $data) {
                if ($type === $otherType || (isset(self::EQUIVALENT_TYPES[$type]) && self::EQUIVALENT_TYPES[$type] === $otherType)) {
                    continue;
                }
                $result["$type with $otherType data"] = [$type, $data];
            }
        }
        return $result;
    }
}