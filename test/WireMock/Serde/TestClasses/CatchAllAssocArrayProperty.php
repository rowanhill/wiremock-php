<?php

namespace WireMock\Serde\TestClasses;

class CatchAllAssocArrayProperty
{
    /** @var int */
    private $topLevel;
    /**
     * @var array<string, string>
     * @serde-catch-all
     */
    private $catchAll;

    /**
     * @param int $topLevel
     * @param array<string, string> $catchAll
     */
    public function __construct(int $topLevel, array $catchAll)
    {
        $this->topLevel = $topLevel;
        $this->catchAll = $catchAll;
    }
}