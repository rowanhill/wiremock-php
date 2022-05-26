<?php

namespace WireMock\SerdeGen\Tag;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

class SerdeNameTag implements Tag
{
    /** @var string */
    private $serializedPropertyName;

    /**
     * @param string $serializedPropertyName
     */
    public function __construct(string $serializedPropertyName)
    {
        $this->serializedPropertyName = $serializedPropertyName;
    }

    public static function create(string $body)
    {
        $propName = trim($body);
        return new static($propName);
    }

    public function getName(): string
    {
        return 'serde-name';
    }

    public function getSerializedPropertyName(): string
    {
        return $this->serializedPropertyName;
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
        return $this->getSerializedPropertyName();
    }
}