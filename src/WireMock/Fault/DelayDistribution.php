<?php

namespace WireMock\Fault;

interface DelayDistribution
{
    public function toArray();

    public static function fromArray(array $array);
}