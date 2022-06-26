<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace WireMock\Serde;

use stdClass;
use WireMock\HamcrestTestCase;
use WireMock\Serde\TestClasses\CatchAllAndNamedByProperty;
use WireMock\Serde\TestClasses\CatchAllAndNamedProperty;
use WireMock\Serde\TestClasses\CatchAllAssocArrayProperty;
use WireMock\Serde\TestClasses\CatchAllTypedArrayProperty;
use WireMock\Serde\TestClasses\CatchAllUntypedArrayProperty;
use WireMock\Serde\TestClasses\ClassTypeFields;
use WireMock\Serde\TestClasses\ConstructorParams;
use WireMock\Serde\TestClasses\DefaultedField;
use WireMock\Serde\TestClasses\DiscriminatedTypeParent;
use WireMock\Serde\TestClasses\DiscriminatedTypeSubclassA;
use WireMock\Serde\TestClasses\NamedByProperty;
use WireMock\Serde\TestClasses\NamedProperty;
use WireMock\Serde\TestClasses\PrimitiveArrayFields;
use WireMock\Serde\TestClasses\FieldOnlyPrimitives;
use WireMock\Serde\TestClasses\UnionTypeFields;
use WireMock\Serde\TestClasses\UnwrappedAndNamedByProperty;
use WireMock\Serde\TestClasses\UnwrappedAndNamedProperty;
use WireMock\Serde\TestClasses\UnwrappedArrayProperty;
use WireMock\Serde\TestClasses\UnwrappedClassProperty;
use WireMock\Serde\TestClasses\UnwrappedPrimitiveProperty;
use WireMock\SerdeGen\SerdeTypeLookupFactory;

class SerializerIntegrationTest extends HamcrestTestCase
{
    /** @dataProvider providerSerializableValues */
    public function testNormalizationAndDenormalization($value, $expectedNormalized, $explicitType = null, $explicitJson = null)
    {
        $typeName = $explicitType ?? (is_object($value) ? get_class($value) : gettype($value));
        $serializer = $this->serializerFor($typeName);

        $normalized = $serializer->normalize($value);
        self::assertEquals($expectedNormalized, $normalized);

        $json = $explicitJson ?? $serializer->serialize($value);
        $deserialized = $serializer->deserialize($json, $typeName);
        self::assertEquals($value, $deserialized);
    }

    public function providerSerializableValues(): array
    {
        $fieldOnlyPrimitives = FieldOnlyPrimitives::create(123, 1.23, true, 'a string');
        $fieldsOnlyPrimitivesNormed = ['int' => 123, 'float' => 1.23, 'bool' => true, 'string' => 'a string'];
        return [
            'bare int' => [123, 123, 'int'],
            'bare float' => [1.23, 1.23, 'float'],
            'bare string' => ['a string', 'a string'],
            'bare bool' => [true, true, 'bool'],
            'bare null' => [null, null, 'null'],

            'class with primitive fields' => [
                $fieldOnlyPrimitives,
                $fieldsOnlyPrimitivesNormed
            ],

            'class with arrays of primitives' => [
                new PrimitiveArrayFields([false], [123], ['key456' => 789]),
                ['untypedArray' => [false], 'intArray' => [123], 'intByString' => ['key456' => 789]]
            ],

            // We represent empty associative arrays as an empty stdClass when normalizing, to ensure it's serialized
            // as "{}" rather than "[]"
            'normalizes empty assoc array to empty stdClass' => [
                new PrimitiveArrayFields([], [], []),
                ['untypedArray' => [], 'intArray' => [], 'intByString' => new stdClass()],
            ],

            // This test provides the JSON to deserialize which includes an ignored constructor arg
            'does not normalize constructor args that don\'t match fields' => [
                new ConstructorParams('ignored', 123),
                ['fieldWithConstructorParam' => 123, 'fieldWithOptionalConstructorParam' => true],
                null,
                json_encode(['fieldWithConstructorParam' => 123, 'fieldWithOptionalConstructorParam' => true, 'ignoredParam' => 'dummy']),
            ],

            'class with nested class type properties' => [
                new ClassTypeFields($fieldOnlyPrimitives),
                ['primitiveTypes' => $fieldsOnlyPrimitivesNormed]
            ],

            'top level untyped array' => [
                [1, 2, 'three'],
                [1, 2, 'three'],
                'array'
            ],

            'top level typed array' => [
                [1, 2, 3],
                [1, 2, 3],
                'int[]'
            ],

            'top level assoc array' => [
                ['foo' => 'bar'],
                ['foo' => 'bar'],
                'array<string,string>'
            ],

            'class with union type properties' => [
                new UnionTypeFields(1, [2, 3], new DefaultedField(), ['four', 5]),
                ['primitivesUnion' => 1, 'arrayUnion' => [2,3], 'classUnion' => ['defaulted' => 'default'], 'arrayOfUnion' => ['four', 5]]
            ],

            'class with @serde-named property' => [
                new NamedProperty(),
                ['serializedName' => 'value']
            ],

            'class with @serde-named-by property' => [
                new NamedByProperty('two', 2),
                ['two' => 2]
            ],

            'class with @serde-unwrapped class type property' => [
                new UnwrappedClassProperty(5678, $fieldOnlyPrimitives),
                array_merge(['topLevel' => 5678], $fieldsOnlyPrimitivesNormed)
            ],

            /**
             * The @serde-name is ignored, but is permitted
             */
            'class with @serde-unwrapped and @serde-name property' => [
                new UnwrappedAndNamedProperty($fieldOnlyPrimitives),
                $fieldsOnlyPrimitivesNormed,
            ],

            'class with @serde-catch-all assoc array type property' => [
                new CatchAllAssocArrayProperty(123, ['foo' => 'a', 'bar' => 'b']),
                ['topLevel' => 123, 'foo' => 'a', 'bar' => 'b']
            ],

            /*
             * Note on PHP quirk:
             * When normalizing, int key values are merged in to the assoc array, alongside string key values.
             * The JSON will hold them as string values (because JSON objects can't have integer keys).
             * When denormalizing, these numeric string values will be passed as array keys, and PHP will automatically
             * convert them to ints - this is simply standard behaviour of PHP arrays!
             * (Note, for the same reason, it's also possible to specify numeric string key values in the expected
             * normalized array and still have the test pass)
             */
            'class with @serde-catch-all non-assoc typed array type property' => [
                new CatchAllTypedArrayProperty(123, ['a', 'b']),
                ['topLevel' => 123, 0 => 'a', 1 => 'b']
            ],

            'class with @serde-catch-all untyped array property' => [
                new CatchAllUntypedArrayProperty(123, ['a', false]),
                ['topLevel' => 123, 0 => 'a', 1 => false]
            ],

            /**
             * The @serde-name is ignored, but is permitted
             */
            'class with @serde-catch-all and @serde-name property' => [
                new CatchAllAndNamedProperty(['foo' => 'a']),
                ['foo' => 'a']
            ],

            'subtype of discriminated type' => [
                new DiscriminatedTypeSubclassA(123),
                ['type' => 'a', 'subclassAProp' => 123]
            ],

            'self type of discriminated type' => [
                new DiscriminatedTypeParent('parent'),
                ['type' => 'parent'],
            ],
        ];
    }

    /** @dataProvider providerBadDeserializeValues */
    public function testThrowsWhenDeserializing($value, $expectedNormalized, $explicitType = null, $explicitJson = null)
    {
        $typeName = $explicitType ?? (is_object($value) ? get_class($value) : gettype($value));
        $serializer = $this->serializerFor($typeName);

        $normalized = $serializer->normalize($value);
        self::assertEquals($expectedNormalized, $normalized);

        $json = $explicitJson ?? $serializer->serialize($value);

        // Expect an exception (with any message) at the deserialize step
        $this->expectExceptionMessageMatches('/.?/');
        $serializer->deserialize($json, $typeName);
    }

    public function providerBadDeserializeValues(): array
    {
        return [
            'missing non-nullable bare int' => [123, 123, 'int', 'null'],
            'missing non-nullable bare float' => [1.23, 1.23, 'float', 'null'],
            'missing non-nullable bare string' => ['a string', 'a string', null, 'null'],
            'missing non-nullable bare bool' => [true, true, 'bool', 'null'],

            'missing non-nullable field value' => [
                FieldOnlyPrimitives::create(123, 1.23, true, 'a string'),
                ['int' => 123, 'float' => 1.23, 'bool' => true, 'string' => 'a string'],
                null,
                ['int' => 123, 'float' => 1.23, 'bool' => true],
            ],

            'php name instead of @serde-name serialized name' => [
                new NamedProperty(),
                ['serializedName' => 'value'],
                null,
                ['originalName' => 'value'],
            ],

            'missing non-nullable @serde-named-by name' => [
                new NamedByProperty('two', 2),
                ['two' => 2],
                null,
                ['not-a-registered-name' => 2],
            ],

            'class with @serde-unwrapped primitive property' => [
                new UnwrappedPrimitiveProperty('cannot be unwrapped'),
                ['unwrappedPrimitive' => 'cannot be unwrapped']
            ],

            'class with @serde-unwrapped array property' => [
                new UnwrappedArrayProperty(['unwraps', 'when', 'serializing']),
                ['unwraps', 'when', 'serializing']
            ],

            'discriminated type that resolves to a type not registered with @serde-possible-subtype' => [
                new DiscriminatedTypeParent('unknown'),
                ['type' => 'unknown'],
            ],
        ];
    }

    /** @dataProvider providerBadSerializeValues */
    public function testThrowsWhenNormalizing($value)
    {
        $typeName = $explicitType ?? (is_object($value) ? get_class($value) : gettype($value));
        $serializer = $this->serializerFor($typeName);

        $this->expectExceptionMessageMatches('/.?/');
        $serializer->normalize($value);
    }

    public function providerBadSerializeValues(): array
    {
        return [
            '@serde-named-by and @serde-unwrapped' => [new UnwrappedAndNamedByProperty()],
            '@serde-named-by and @serde-catch-all' => [new CatchAllAndNamedByProperty()],
        ];
    }

    // TODO: Associative arrays with string keys behave unexpectedly when a key's value is a decimal number

    private function serializerFor(string $className): Serializer
    {
        $lookup = SerdeTypeLookupFactory::createLookup($className);
        return new Serializer($lookup);
    }
}