<?php

namespace WireMock\Client;

class FindRequestsResult
{
    /** @var LoggedRequest[] */
    private $requests;

    /**
     * @param LoggedRequest[] $requests
     */
    public function __construct(array $requests)
    {
        $this->requests = $requests;
    }

    /**
     * @return LoggedRequest[]
     */
    public function getRequests(): array
    {
        return $this->requests;
    }
}