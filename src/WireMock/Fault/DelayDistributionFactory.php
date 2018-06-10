<?php

namespace WireMock\Fault;

class DelayDistributionFactory
{
    /**
     * @param array $array
     * @return DelayDistribution
     * @throws \Exception
     */
    public static function fromArray(array $array)
    {
        if ($array['type'] === 'lognormal') {
            return LogNormal::fromArray($array);
        } else if ($array['type'] === 'uniform') {
            return UniformDistribution::fromArray($array);
        } else {
            throw new \Exception("Unknown DelayDistribution type '${$array['type']}'");
        }
    }
}