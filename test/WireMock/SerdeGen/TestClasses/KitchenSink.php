<?php

namespace WireMock\SerdeGen\TestClasses;

/**
 * @serde-discriminate-type discriminate
 * @serde-possible-subtype KitchenSinkSubA
 * @serde-possible-subtype KitchenSinkSubB
 */
class KitchenSink
{
    private static function discriminate() {}

    /** @var string */
    private $fieldOnly;

    /** @var int */
    private $reqArg;

    /**
     * @var bool
     * @serde-name renamed
     */
    private $originalName;

    /** @var string */
    private $namedByName;
    /**
     * @var int
     * @serde-named-by namedByName
     * @serde-possible-names possibleNames
     */
    private $namedByValue;
    private static function possibleNames() {}

    /** @var OneSimpleField */
    private $object;

    /**
     * @var OneSimpleField
     * @serde-unwrapped
     */
    private $inlined;

    /**
     * @var array
     * @serde-catch-all
     */
    private $catchall;

    public function __construct(int $reqArg, $ignoredParam = null)
    {
        $this->reqArg = $reqArg;
    }
}