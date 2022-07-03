<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace WireMock\Serde;

use JsonSerializable;
use Phake;
use WireMock\HamcrestTestCase;
use WireMock\Serde\Type\SerdeTypeClass;
use WireMock\Serde\Type\SerdeTypeLookup;

class SerializerTest extends HamcrestTestCase
{
    /** @dataProvider providerPrimitives */
    public function testNormalizingPrimitivesIsANoOp($primitiveValue)
    {
        $serializer = new Serializer(new SerdeTypeLookup([], []));

        $normalized = $serializer->normalize($primitiveValue);

        assertThat($normalized, is($primitiveValue));
    }

    public function testNormalizingObjectRegisteredInLookupDelegatesToSerdeTypeClass()
    {
        $mockSerdeType = Phake::mock(SerdeTypeClass::class);
        $serializer = new Serializer(new SerdeTypeLookup([self::class => $mockSerdeType], []));
        Phake::when($mockSerdeType)->normalize($this, $serializer)->thenReturn(['dummy']);

        $normalized = $serializer->normalize($this);

        assertThat($normalized, is(['dummy']));
    }

    public function testSerializingObjectNotInLookupObservesJsonSerializableInterface()
    {
        $object = new class implements JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['foo' => 'bar'];
            }
        };
        $mockSerdeType = Phake::mock(SerdeTypeClass::class);
        $serializer = new Serializer(new SerdeTypeLookup([self::class => $mockSerdeType], [self::class => true]));
        Phake::when($mockSerdeType)->normalize($this, $serializer)->thenReturn(['unregistered' => [$object]]);

        $json = $serializer->serialize($this);

        self::assertEquals('{"unregistered":[{"foo":"bar"}]}', $json);
    }

    public function testNormalizingArrayRecursivelyDenormalizesKeepingKeys()
    {
        $mockSerdeType = Phake::mock(SerdeTypeClass::class);
        $serializer = new Serializer(new SerdeTypeLookup([self::class => $mockSerdeType], []));
        Phake::when($mockSerdeType)->normalize($this, $serializer)->thenReturn(['dummy']);

        $normalized = $serializer->normalize(['one' => $this, 'two' => 123]);

        assertThat($normalized, is(['one' => ['dummy'], 'two' => 123]));
    }

    public function testDenormalizingDelegatesToSerdeType()
    {
        $mockSerdeType = Phake::mock(SerdeTypeClass::class);
        $serializer = new Serializer(new SerdeTypeLookup([self::class => $mockSerdeType], []));
        $data = ['dummy'];
        Phake::when($mockSerdeType)->denormalize($data, [])->thenReturn($this);

        $denormalized = $serializer->denormalize($data, self::class);

        assertThat($denormalized, is($this));
    }

    public function providerPrimitives(): array
    {
        return [
            'string' => ['a string'],
            'int' => [123],
            'float' => [1.23],
            'bool' => [false],
            'null' => [null],
        ];
    }
}