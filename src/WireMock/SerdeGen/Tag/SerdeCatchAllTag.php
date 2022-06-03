<?php

namespace WireMock\SerdeGen\Tag;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

class SerdeCatchAllTag implements Tag
{
    public static function create(string $body)
    {
        return new static();
    }

    public function getName(): string
    {
        return 'serde-catch-all';
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
        return '';
    }
}