<?php

namespace WireMock\Serde\TestClasses;

class NamedByProperty
{
    /**
     * @var string
     */
    private $valueName;
    /**
     * @var int
     * @serde-named-by valueName
     * @serde-possible-names names
     */
    private $value;

    /**
     * @param string $valueName
     * @param int $value
     */
    public function __construct(string $valueName, int $value)
    {
        $this->valueName = $valueName;
        $this->value = $value;
    }

    private static function names(): array
    {
        return ['one', 'two', 'three'];
    }
}