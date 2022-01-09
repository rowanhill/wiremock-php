<?php

namespace WireMock\Fault;

use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use WireMock\Serde\MappingProvider;

abstract class DelayDistribution implements MappingProvider
{
    /** @var string */
    private $type;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function getDiscriminatorMapping(): ClassDiscriminatorMapping
    {
        return new ClassDiscriminatorMapping('type', [
            'lognormal' => LogNormal::class,
            'uniform' => UniformDistribution::class
        ]);
    }
}