<?php

namespace WireMock\SerdeGen\Tag;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

class SerdePossibleNamesTag implements Tag
{
    /** @var string */
    private $possibleNamesGenerator;

    /**
     * @param string $possibleNamesGenerator
     */
    public function __construct(string $possibleNamesGenerator)
    {
        $this->possibleNamesGenerator = $possibleNamesGenerator;
    }

    public static function create(string $body)
    {
        $propName = trim($body);
        return new static($propName);
    }

    public function getName(): string
    {
        return 'serde-possible-names';
    }

    public function getPossibleNamesGenerator(): string
    {
        return $this->possibleNamesGenerator;
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
        return $this->getPossibleNamesGenerator();
    }
}