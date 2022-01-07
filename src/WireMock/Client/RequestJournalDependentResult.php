<?php

namespace WireMock\Client;

abstract class RequestJournalDependentResult extends PaginatedResult
{
    /** @var boolean */
    private $_requestJournalDisabled;

    /**
     * @param Meta $meta
     * @param bool $requestJournalDisabled
     */
    public function __construct(Meta $meta, bool $requestJournalDisabled)
    {
        parent::__construct($meta);
        $this->_requestJournalDisabled = $requestJournalDisabled;
    }

    /**
     * @return bool
     */
    public function isRequestJournalDisabled()
    {
        return $this->_requestJournalDisabled;
    }
}