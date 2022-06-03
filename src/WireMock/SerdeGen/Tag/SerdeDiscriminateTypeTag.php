<?php

namespace WireMock\SerdeGen\Tag;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

class SerdeDiscriminateTypeTag implements Tag
{
    /** @var string */
    private $discriminatorFactory;

    /**
     * @param string $discriminatorFactory
     */
    public function __construct(string $discriminatorFactory)
    {
        $this->discriminatorFactory = $discriminatorFactory;
    }

    public static function create(string $body)
    {
        return new static(trim($body));
    }

    public function getName(): string
    {
        return 'serde-discriminate-type';
    }

    public function getDiscriminatorFactory(): string
    {
        return $this->discriminatorFactory;
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
        return $this->getDiscriminatorFactory();
    }
}