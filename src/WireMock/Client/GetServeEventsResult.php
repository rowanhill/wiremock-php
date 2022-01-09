<?php

namespace WireMock\Client;

class GetServeEventsResult extends RequestJournalDependentResult
{
    /** @var ServeEvent[]  */
    private $requests;

    /**
     * @param Meta $meta
     * @param bool $requestJournalDisabled
     * @param ServeEvent[] $requests
     */
    public function __construct(Meta $meta, bool $requestJournalDisabled, array $requests)
    {
        parent::__construct($meta, $requestJournalDisabled);
        $this->requests = $requests;
    }

    /**
     * @return ServeEvent[]
     */
    public function getRequests()
    {
        return $this->requests;
    }
}