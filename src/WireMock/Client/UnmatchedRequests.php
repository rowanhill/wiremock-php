<?php

namespace WireMock\Client;

class UnmatchedRequests
{
    /** @var LoggedRequest[] */
    private $_requests;
    /** @var boolean */
    private $_requestJournalDisabled;

    /**
     * UnmatchedRequests constructor.
     * @param array $array
     */
    public function __construct(array $array)
    {
        $requests = array();
        foreach ($array['requests'] as $responseArray) {
            $requests[] = new LoggedRequest($responseArray);
        }
        $this->_requests = $requests;

        $this->_requestJournalDisabled = $array['requestJournalDisabled'];
    }

    /**
     * @return LoggedRequest[]
     */
    public function getRequests()
    {
        return $this->_requests;
    }

    /**
     * @return boolean
     */
    public function getRequestJournalDisabled()
    {
        return $this->_requestJournalDisabled;
    }
}