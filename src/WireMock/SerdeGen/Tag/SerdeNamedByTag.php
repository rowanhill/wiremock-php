<?php

namespace WireMock\SerdeGen\Tag;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

class SerdeNamedByTag implements Tag
{
    /** @var string */
    private $namingPropertyName;

    /**
     * @param string $namingPropertyName
     */
    public function __construct(string $namingPropertyName)
    {
        $this->namingPropertyName = $namingPropertyName;
    }

    public static function create(string $body)
    {
        $propName = trim($body);
        return new static($propName);
    }

    public function getName(): string
    {
        return 'serde-named-by';
    }

    public function getNamingPropertyName(): string
    {
        return $this->namingPropertyName;
    }

    public function render(?Formatter $formatter = null): string
    {
        if ($formatter === null) {
            $formatter = new Formatter\PassthroughFormatter();
        }

        return $formatter->format($this);
    }

    public function __toString(): string
    {
        return $this->getNamingPropertyName();
    }
}