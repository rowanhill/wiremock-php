<?php

namespace WireMock\Client;

class CountMatchingRequestsResult
{
    private $count;

    /**
     * @param $count
     */
    public function __construct($count)
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }
}