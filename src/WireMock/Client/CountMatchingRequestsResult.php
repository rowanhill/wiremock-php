<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class CountMatchingRequestsResult implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    private $_count;

    /**
     * @param $count
     */
    public function __construct($count)
    {
        $this->_count = $count;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->_count;
    }
}