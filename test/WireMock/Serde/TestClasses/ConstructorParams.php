<?php

namespace WireMock\Serde\TestClasses;

class ConstructorParams
{
    /** @var int */
    private $fieldWithConstructorParam;
    /** @var bool */
    private $fieldWithOptionalConstructorParam;

    /**
     * @param string $ignoredParam
     * @param int $fieldWithConstructorParam
     * @param bool $fieldWithOptionalConstructorParam
     */
    public function __construct(string $ignoredParam, int $fieldWithConstructorParam, bool $fieldWithOptionalConstructorParam = true)
    {
        $this->fieldWithConstructorParam = $fieldWithConstructorParam;
        $this->fieldWithOptionalConstructorParam = $fieldWithOptionalConstructorParam;
    }
}