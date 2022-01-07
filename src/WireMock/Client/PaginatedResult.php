<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

abstract class PaginatedResult
{
    /** @var Meta */
    private $_meta;

    /**
     * @param Meta $meta
     */
    public function __construct(Meta $meta)
    {
        $this->_meta = $meta;
    }

    /**
     * @return Meta
     */
    public function getMeta()
    {
        return $this->_meta;
    }
}

class Meta implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
    /** @var int */
    private $_total;

    /**
     * @param int $total
     */
    public function __construct(int $total)
    {
        $this->_total = $total;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->_total;
    }
}