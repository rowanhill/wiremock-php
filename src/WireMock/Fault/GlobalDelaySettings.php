<?php

namespace WireMock\Fault;

abstract class GlobalDelaySettings
{
    /**
     * @param int $millis
     * @return array{delayDistribution: null, fixedDelay: int}
     */
    public static function fixed($millis)
    {
        return ['delayDistribution' => null, 'fixedDelay' => $millis];
    }

    /**
     * @param DelayDistribution $distribution
     * @return array{delayDistribution: DelayDistribution, fixedDelay: null}
     */
    public static function random($distribution)
    {
        return ['delayDistribution' => $distribution, 'fixedDelay' => null];
    }

    /**
     * @return array{delayDistribution: null, fixedDelay: null}
     */
    public static function none()
    {
        return ['delayDistribution' => null, 'fixedDelay' => null];
    }
}