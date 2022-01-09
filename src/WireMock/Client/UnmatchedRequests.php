<?php

namespace WireMock\Client;

class UnmatchedRequests
{
    /** @var LoggedRequest[] */
    private $requests;
    /** @var boolean */
    private $requestJournalDisabled;

    /**
     * @param bool $requestJournalDisabled
     * @param LoggedRequest[] $requests
     */
    public function __construct(bool $requestJournalDisabled, array $requests)
    {
        $this->requestJournalDisabled = $requestJournalDisabled;
        $this->requests = $requests;
    }

    /**
     * @return LoggedRequest[]
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @return boolean
     */
    public function getRequestJournalDisabled()
    {
        return $this->requestJournalDisabled;
    }
}