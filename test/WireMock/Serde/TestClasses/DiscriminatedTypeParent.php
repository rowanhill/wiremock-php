<?php

namespace WireMock\Serde\TestClasses;

use WireMock\Serde\ClassDiscriminatorMapping;

/**
 * @serde-discriminate-type getDiscriminatorMapping
 * @serde-possible-subtype DiscriminatedTypeParent
 * @serde-possible-subtype DiscriminatedTypeSubclassA
 * @serde-possible-subtype DiscriminatedTypeSubclassB
 */
class DiscriminatedTypeParent
{
    /** @var string */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    private static function getDiscriminatorMapping()
    {
        return new ClassDiscriminatorMapping('type', [
            'parent' => DiscriminatedTypeParent::class,
            'a' => DiscriminatedTypeSubclassA::class,
            'b' => DiscriminatedTypeSubclassB::class,

            // Will result in exception at deserialization time, because this is not a registered possible subtype
            'unknown' => FieldOnlyPrimitives::class,
        ]);
    }
}