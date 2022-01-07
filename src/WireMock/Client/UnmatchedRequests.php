<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class UnmatchedRequests implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
    /** @var LoggedRequest[] */
    private $_requests;
    /** @var boolean */
    private $_requestJournalDisabled;

    /**
     * @param bool $requestJournalDisabled
     * @param LoggedRequest[] $requests
     */
    public function __construct(bool $requestJournalDisabled, array $requests)
    {
        $this->_requestJournalDisabled = $requestJournalDisabled;
        $this->_requests = $requests;
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