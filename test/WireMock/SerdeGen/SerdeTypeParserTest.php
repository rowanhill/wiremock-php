<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace WireMock\SerdeGen;

use WireMock\HamcrestTestCase;
use WireMock\Serde\PropNaming\ConstantPropertyNamingStrategy;
use WireMock\Serde\PropNaming\ReferencingPropertyNamingStrategy;
use WireMock\Serde\SerdeClassDefinition;
use WireMock\Serde\SerdeClassDiscriminationInfo;
use WireMock\Serde\SerdeProp;
use WireMock\Serde\Type\SerdeTypeAssocArray;
use WireMock\Serde\Type\SerdeTypeClass;
use WireMock\Serde\Type\SerdeTypeNull;
use WireMock\Serde\Type\SerdeTypePrimitive;
use WireMock\Serde\Type\SerdeTypeTypedArray;
use WireMock\Serde\Type\SerdeTypeUnion;
use WireMock\Serde\Type\SerdeTypeUntypedArray;
use WireMock\SerdeGen\TestClasses\KitchenSink;
use WireMock\SerdeGen\TestClasses\KitchenSinkSubA;
use WireMock\SerdeGen\TestClasses\KitchenSinkSubB;
use WireMock\SerdeGen\TestClasses\OneSimpleField;

class SerdeTypeParserTest extends HamcrestTestCase
{
    /** @var PartialSerdeTypeLookup */
    private $lookup;
    /** @var SerdeTypeParser */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lookup = new PartialSerdeTypeLookup();
        $this->parser = new SerdeTypeParser($this->lookup);
    }

    public function testParsingNull()
    {
        $serdeType = $this->parser->parseTypeString('null');
        assertThat($serdeType, self::isInstanceOf(SerdeTypeNull::class));
    }

    /** @dataProvider providerPrimitiveTypes */
    public function testParsingPrimitive($inputType, $outputType)
    {
        $serdeType = $this->parser->parseTypeString($inputType);
        self::assertEquals(new SerdeTypePrimitive($outputType), $serdeType);
    }

    /** @dataProvider providerPrimitiveTypes */
    public function testParsingQuestionMarkNullablePrimitive($inputType, $outputType)
    {
        $serdeType = $this->parser->parseTypeString('?'.$inputType);
        self::assertEquals(
            new SerdeTypeUnion([new SerdeTypePrimitive($outputType), new SerdeTypeNull()], null),
            $serdeType
        );
    }

    /** @dataProvider providerPrimitiveTypes */
    public function testParsingUnionNullableBeforePrimitive($inputType, $outputType)
    {
        $serdeType = $this->parser->parseTypeString('null|'.$inputType);
        self::assertEquals(
            new SerdeTypeUnion([new SerdeTypeNull(), new SerdeTypePrimitive($outputType)], null),
            $serdeType
        );
    }

    /** @dataProvider providerPrimitiveTypes */
    public function testParsingUnionNullableAfterPrimitive($inputType, $outputType)
    {
        $serdeType = $this->parser->parseTypeString($inputType.'|null');
        self::assertEquals(
            new SerdeTypeUnion([new SerdeTypePrimitive($outputType), new SerdeTypeNull()], null),
            $serdeType
        );
    }

    /** @dataProvider providerUnsupportedTypes */
    public function testParsingUnsupportedTypes($type)
    {
        $this->expectExceptionMessage('Unsupported type');
        $this->parser->parseTypeString($type);
    }

    public function testParsingUntypedArray()
    {
        $serdeType = $this->parser->parseTypeString('array');
        self::assertEquals(new SerdeTypeUntypedArray(), $serdeType);
    }

    public function testParsingQuestionMarkNullableUntypedArray()
    {
        $serdeType = $this->parser->parseTypeString('?array');
        self::assertEquals(new SerdeTypeUnion([new SerdeTypeNull()], new SerdeTypeUntypedArray()), $serdeType);
    }

    /** @dataProvider providerPrimitiveTypes */
    public function testParsingArrayOfPrimitives($inputType, $outputType)
    {
        $serdeType = $this->parser->parseTypeString($inputType.'[]');
        self::assertEquals(
            new SerdeTypeTypedArray(new SerdeTypePrimitive($outputType)),
            $serdeType
        );
    }

    /** @dataProvider providerPrimitiveAssocArrayTypes */
    public function testParsingAssocArrayOfPrimitives($keyInputType, $keyOutputType, $valueInputType, $valueOutputType)
    {
        $serdeType = $this->parser->parseTypeString("array<$keyInputType, $valueInputType>");
        self::assertEquals(
            new SerdeTypeAssocArray(new SerdeTypePrimitive($keyOutputType), new SerdeTypePrimitive($valueOutputType)),
            $serdeType
        );
    }

    /** @dataProvider providerOtherPrimitiveTypes */
    public function testParsingAssocArrayWithBadPrimitiveKeyTypeIsNotSupported($keyInputType)
    {
        $this->expectErrorMessage('An array can have only integers or strings as keys');
        $this->parser->parseTypeString("array<$keyInputType, int>");
    }

    /** @dataProvider providerArrayKeyTypes */
    public function testParsingAssocArrayWithNonPrimitiveKeyTypeIsNotSupported($keyInputType)
    {
        $this->expectErrorMessage('An array can have only integers or strings as keys');
        $this->parser->parseTypeString("array<${keyInputType}[], int>");
    }

    public function testParsingUnionOfPrimitivesAndNull()
    {
        $serdeType = $this->parser->parseTypeString('int|string|null|bool|float');
        self::assertEquals(
            new SerdeTypeUnion(
                [
                    new SerdeTypePrimitive('int'),
                    new SerdeTypePrimitive('string'),
                    new SerdeTypeNull(),
                    new SerdeTypePrimitive('bool'),
                    new SerdeTypePrimitive('float'),
                ],
                null
            ),
            $serdeType
        );
    }

    public function testParsingUnionWithDuplicatedTypes()
    {
        $serdeType = $this->parser->parseTypeString('int|integer|bool|boolean|float|double');
        self::assertEquals(
            new SerdeTypeUnion(
                [
                    new SerdeTypePrimitive('int'),
                    new SerdeTypePrimitive('bool'),
                    new SerdeTypePrimitive('float'),
                ],
                null
            ),
            $serdeType
        );
    }

    public function testParsingUnionWithUntypedArray()
    {
        $serdeType = $this->parser->parseTypeString('int|array');
        self::assertEquals(
            new SerdeTypeUnion([new SerdeTypePrimitive('int')], new SerdeTypeUntypedArray()),
            $serdeType
        );
    }

    public function testParsingUnionWithTypedArray()
    {
        $serdeType = $this->parser->parseTypeString('int|int[]');
        self::assertEquals(
            new SerdeTypeUnion([new SerdeTypePrimitive('int')], new SerdeTypeTypedArray(new SerdeTypePrimitive('int'))),
            $serdeType
        );
    }

    public function testParsingUnionWithAssocArray()
    {
        $serdeType = $this->parser->parseTypeString('int|array<string, int>');
        self::assertEquals(
            new SerdeTypeUnion(
                [new SerdeTypePrimitive('int')],
                new SerdeTypeAssocArray(new SerdeTypePrimitive('string'), new SerdeTypePrimitive('int'))
            ),
            $serdeType
        );
    }

    public function testParsingUnionWithMultipleArrayTypesIsNotSupported()
    {
        $this->expectErrorMessage('more than one non-primitive');
        $this->parser->parseTypeString('array|int[]');
    }

    public function testParsingQuestionMarkNullableUnionIsNotSupported()
    {
        $this->expectErrorMessage('Nullable unions (using ?) are currently unsupported');
        $this->parser->parseTypeString('?(int|string)');
    }

    public function testParsingQuestionMarkNullableNullIsNotSupported()
    {
        $this->expectErrorMessage('Unexpected nullable type');
        $this->parser->parseTypeString('?null');
    }

    public function testParsingArrayOfUnion()
    {
        $serdeType = $this->parser->parseTypeString('(int|string)[]');
        self::assertEquals(
            new SerdeTypeTypedArray(new SerdeTypeUnion(
                [new SerdeTypePrimitive('int'), new SerdeTypePrimitive('string')],
                null
            )),
            $serdeType
        );
    }

    public function testParsingAssocArrayWithUnionValues()
    {
        $serdeType = $this->parser->parseTypeString('array<int, int|string>');
        self::assertEquals(
            new SerdeTypeAssocArray(
                new SerdeTypePrimitive('int'),
                new SerdeTypeUnion(
                    [new SerdeTypePrimitive('int'), new SerdeTypePrimitive('string')],
                    null
                )
            ),
            $serdeType
        );
    }

    public function testParsingKitchenSinkClass()
    {
        $serdeType = $this->parser->parseTypeString(KitchenSink::class);

        $string = new SerdeTypePrimitive('string');
        $int = new SerdeTypePrimitive('int');
        $bool = new SerdeTypePrimitive('bool');
        $argAndFieldProp = new SerdeProp('reqArg', KitchenSink::class, $int);
        $oneSimpleFieldType = $this->getOneSimpleFieldType();
        $kitchenSinkProperties = [
            // Simple field prop
            new SerdeProp(
                'fieldOnly',
                KitchenSink::class,
                $string
            ),

            // Prop appears as both field and required constructor arg
            $argAndFieldProp,

            // Prop is renamed
            new SerdeProp(
                'originalName',
                KitchenSink::class,
                $bool,
                new ConstantPropertyNamingStrategy('renamed')
            ),

            // One field named by another
            new SerdeProp(
                'namedByName',
                KitchenSink::class,
                $string
            ),
            new SerdeProp(
                'namedByValue',
                KitchenSink::class,
                $int,
                new ReferencingPropertyNamingStrategy(
                    'namedByName',
                    KitchenSink::class . '::possibleNames'
                )
            ),

            // Field with a class type
            new SerdeProp(
                'object',
                KitchenSink::class,
                $oneSimpleFieldType
            ),

            // Unwrapped
            new SerdeProp(
                'inlined',
                KitchenSink::class,
                $oneSimpleFieldType,
                null,
                true
            ),

            // Catch-all
            new SerdeProp(
                'catchall',
                KitchenSink::class,
                new SerdeTypeUntypedArray(),
                null,
                false,
                true
            )
        ];
        self::assertEquals(
            new SerdeTypeClass(
                '\\'.KitchenSink::class,
                new SerdeClassDefinition(
                    new SerdeClassDiscriminationInfo(
                        KitchenSink::class.'::discriminate',
                        [
                            KitchenSinkSubA::class => new SerdeTypeClass(
                                '\\'.KitchenSinkSubA::class,
                                new SerdeClassDefinition(null, [$argAndFieldProp], $kitchenSinkProperties)
                            ),
                            KitchenSinkSubB::class => new SerdeTypeClass(
                                '\\'.KitchenSinkSubB::class,
                                new SerdeClassDefinition(null, [$argAndFieldProp], $kitchenSinkProperties)
                            )
                        ]
                    ),
                    [$argAndFieldProp],
                    $kitchenSinkProperties
                )
            ),
            $serdeType
        );
    }

    public function testParsingClassAddsTypeToLookupAsRootType()
    {
        $this->parser->parseTypeString(KitchenSink::class);
        self::assertTrue($this->lookup->contains(KitchenSink::class));
        self::assertTrue($this->lookup->isRootType(KitchenSink::class));
    }

    public function testParsingDiscriminatedTypeAddsPossibleSubclassesToLookupAsRootTypes()
    {
        $this->parser->parseTypeString(KitchenSink::class);
        self::assertTrue($this->lookup->contains(KitchenSinkSubA::class));
        self::assertTrue($this->lookup->contains(KitchenSinkSubB::class));
        self::assertTrue($this->lookup->isRootType(KitchenSinkSubA::class));
        self::assertTrue($this->lookup->isRootType(KitchenSinkSubB::class));
    }

    public function testParsingClassAddsClassTypesOfPropertiesToLookupAsNonRootType()
    {
        $this->parser->parseTypeString(KitchenSink::class);
        self::assertTrue($this->lookup->contains(OneSimpleField::class));
        self::assertFalse($this->lookup->isRootType(OneSimpleField::class));
    }

    public function testParsingArrayOfClass()
    {
        $serdeType = $this->parser->parseTypeString(OneSimpleField::class.'[]');
        self::assertEquals(
            new SerdeTypeTypedArray($this->getOneSimpleFieldType()),
            $serdeType
        );
    }

    public function testParsingArrayOfClassAddsClassToLookupAsRootType()
    {
        $this->parser->parseTypeString(OneSimpleField::class.'[]');
        self::assertTrue($this->lookup->contains(OneSimpleField::class));
        self::assertTrue($this->lookup->isRootType(OneSimpleField::class));
    }

    public function testParsingAssocArrayWithClassValues()
    {
        $serdeType = $this->parser->parseTypeString('array<string, '.OneSimpleField::class.'>');
        self::assertEquals(
            new SerdeTypeAssocArray(new SerdeTypePrimitive('string'), $this->getOneSimpleFieldType()),
            $serdeType
        );
    }

    public function testParsingAssocArrayWithClassValuesAddsClassToLookupAsRootType()
    {
        $this->parser->parseTypeString('array<string, '.OneSimpleField::class.'>');
        self::assertTrue($this->lookup->contains(OneSimpleField::class));
        self::assertTrue($this->lookup->isRootType(OneSimpleField::class));
    }

    public function testParsingQuestionMarkNullableClass()
    {
        $serdeType = $this->parser->parseTypeString('?'.OneSimpleField::class);
        self::assertEquals(
            new SerdeTypeUnion([new SerdeTypeNull()], $this->getOneSimpleFieldType()),
            $serdeType
        );
    }

    public function testParsingUnionIncludingClass()
    {
        $serdeType = $this->parser->parseTypeString(OneSimpleField::class.'|null|int');
        self::assertEquals(
            new SerdeTypeUnion([new SerdeTypeNull(), new SerdeTypePrimitive('int')], $this->getOneSimpleFieldType()),
            $serdeType
        );
    }

    public function testParsingUnionIncludingClassAddsClassToLookupAsRootType()
    {
        $this->parser->parseTypeString(OneSimpleField::class.'|null|int');
        self::assertTrue($this->lookup->contains(OneSimpleField::class));
        self::assertTrue($this->lookup->isRootType(OneSimpleField::class));
    }

    public function providerPrimitiveTypes(): array
    {
        return array_merge($this->providerArrayKeyTypes(), $this->providerOtherPrimitiveTypes());
    }

    public function providerArrayKeyTypes(): array
    {
        return [
            'int' => ['int', 'int'],
            'integer' => ['integer', 'int'],
            'string' => ['string', 'string'],
        ];
    }

    public function providerOtherPrimitiveTypes(): array
    {
        return [
            'bool' => ['bool', 'bool'],
            'boolean' => ['boolean', 'bool'],
            'float' => ['float', 'float'],
            'double' => ['double', 'float'],
        ];
    }

    public function providerUnsupportedTypes(): array
    {
        $types = [
            'object',
            'mixed',
            'resource',
            'void',
            'callable',
            'false',
            'true',
            'self',
            'scalar',
        ];
        $result = [];
        foreach ($types as $type) {
            $result[$type] = [$type];
        }
        return $result;
    }

    public function providerPrimitiveAssocArrayTypes(): array
    {
        $arrayKeys = $this->providerArrayKeyTypes();
        $primitives = $this->providerPrimitiveTypes();
        $result = [];
        foreach ($arrayKeys as [$keyIn, $keyOut]) {
            foreach ($primitives as [$valIn, $valOut]) {
                $result["$keyIn,$valIn"] = [$keyIn, $keyOut, $valIn, $valOut];
            }
        }
        return $result;
    }

    private function getOneSimpleFieldType(): SerdeTypeClass
    {
        return new SerdeTypeClass(
            '\\'.OneSimpleField::class,
            new SerdeClassDefinition(
                null,
                [],
                [
                    new SerdeProp(
                        'field',
                        OneSimpleField::class,
                        new SerdeTypePrimitive('int')
                    )
                ]
            )
        );
    }
}