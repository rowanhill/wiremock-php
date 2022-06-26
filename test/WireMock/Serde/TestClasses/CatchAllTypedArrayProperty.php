<?php

namespace WireMock\Serde\TestClasses;

class CatchAllTypedArrayProperty
{
    /** @var int */
    private $topLevel;
    /**
     * @var string[]
     * @serde-catch-all
     */
    private $catchAll;

    /**
     * @param int $topLevel
     * @param string[] $catchAll
     */
    public function __construct(int $topLevel, array $catchAll)
    {
        $this->topLevel = $topLevel;
        $this->catchAll = $catchAll;
    }
}