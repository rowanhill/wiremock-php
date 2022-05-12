<?php

namespace WireMock\Client;

class CountMatchingRequestsResult
{
    /** @var integer */
    private $count;

    /**
     * @param $count
     */
    public function __construct($count)
    {
        $this->count = $count;
    }

    /**
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }
}