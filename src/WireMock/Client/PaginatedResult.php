<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

abstract class PaginatedResult
{
    /** @var Meta */
    private $meta;

    /**
     * @param Meta $meta
     */
    public function __construct(Meta $meta)
    {
        $this->meta = $meta;
    }

    /**
     * @return Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }
}

class Meta implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
    /** @var int */
    private $total;

    /**
     * @param int $total
     */
    public function __construct(int $total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}