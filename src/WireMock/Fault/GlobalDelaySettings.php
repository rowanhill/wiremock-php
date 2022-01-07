<?php

namespace WireMock\Fault;

abstract class GlobalDelaySettings
{
    public static function fixed($millis)
    {
        return ['delayDistribution' => null, 'fixedDelay' => $millis];
    }

    public static function random($distribution)
    {
        return ['delayDistribution' => $distribution, 'fixedDelay' => null];
    }

    public static function none()
    {
        return ['delayDistribution' => null, 'fixedDelay' => null];
    }
}