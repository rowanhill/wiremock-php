<?php

namespace WireMock\SerdeGen\Tag;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Webmozart\Assert\Assert;

class SerdePossibleSubtypeTag extends TagWithType
{
    public function __construct(Type $type, ?Description $description = null)
    {
        $this->name         = 'serde-possible-subtype';
        $this->type         = $type;
        $this->description  = $description;
    }

    public static function create(string $body, ?TypeResolver $typeResolver = null, ?TypeContext $context = null)
    {
        Assert::notNull($typeResolver);
        [$typeName, $body] = self::extractTypeFromBody($body);
        $type = $typeResolver->resolve($typeName, $context);
        $description = new Description($body);

        return new static($type, $description);
    }

    public function __toString(): string
    {
        $type = (string) $this->type;
        if ($this->description) {
            $description = ' ' . $this->description->render();
        } else {
            $description = '';
        }
        return "$type$description";
    }
}