<?php

namespace WireMock\Serde\TestClasses;

class CatchAllUntypedArrayProperty
{
    /** @var int */
    private $topLevel;
    /**
     * @var array
     * @serde-catch-all
     */
    private $catchAll;

    /**
     * @param int $topLevel
     * @param array $catchAll
     */
    public function __construct(int $topLevel, array $catchAll)
    {
        $this->topLevel = $topLevel;
        $this->catchAll = $catchAll;
    }
}