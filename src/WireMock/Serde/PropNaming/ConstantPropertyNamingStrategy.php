<?php

namespace WireMock\Serde\PropNaming;

class ConstantPropertyNamingStrategy implements PropertyNamingStrategy
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    function getSerializedName(array $data): string
    {
        return $this->name;
    }
}