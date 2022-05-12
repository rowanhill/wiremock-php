<?php

namespace WireMock\Fault;

use WireMock\Serde\ClassDiscriminator;
use WireMock\Serde\ClassDiscriminatorMapping;
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

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public static function getDiscriminatorMapping(): ClassDiscriminator
    {
        return new ClassDiscriminatorMapping('type', [
            'lognormal' => LogNormal::class,
            'uniform' => UniformDistribution::class,
            'fixed' => FixedDelay::class
        ]);
    }
}