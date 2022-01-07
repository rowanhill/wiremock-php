<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class FindRequestsResult implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var LoggedRequest[] */
    private $_requests;

    /**
     * @param LoggedRequest[] $_requests
     */
    public function __construct(array $_requests)
    {
        $this->_requests = $_requests;
    }

    /**
     * @return LoggedRequest[]
     */
    public function getRequests(): array
    {
        return $this->_requests;
    }
}