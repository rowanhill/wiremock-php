<?php

namespace WireMock\Serde;

interface ClassDiscriminator
{
    function getDiscriminatedType($data): string;
}