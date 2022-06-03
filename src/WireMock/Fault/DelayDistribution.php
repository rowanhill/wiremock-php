<?php

namespace WireMock\Fault;

use WireMock\Serde\ClassDiscriminator;
use WireMock\Serde\ClassDiscriminatorMapping;

/**
 * @serde-discriminate-type getDiscriminatorMapping
 * @serde-possible-subtype LogNormal
 * @serde-possible-subtype UniformDistribution
 * @serde-possible-subtype FixedDelay
 */
abstract class DelayDistribution
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

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function getDiscriminatorMapping(): ClassDiscriminator
    {
        return new ClassDiscriminatorMapping('type', [
            'lognormal' => LogNormal::class,
            'uniform' => UniformDistribution::class,
            'fixed' => FixedDelay::class
        ]);
    }
}