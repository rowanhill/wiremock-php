<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class FindRequestsResult implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var LoggedRequest[] */
    private $requests;

    /**
     * @param LoggedRequest[] $_requests
     */
    public function __construct(array $_requests)
    {
        $this->requests = $_requests;
    }

    /**
     * @return LoggedRequest[]
     */
    public function getRequests(): array
    {
        return $this->requests;
    }
}