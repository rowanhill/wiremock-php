<?php

namespace WireMock\Serde\PropNaming;

interface PropertyNamingStrategy
{
    function getSerializedName(array $data): string;

    /**
     * @return string[]
     */
    function getPossibleSerializedNames(): array;
}