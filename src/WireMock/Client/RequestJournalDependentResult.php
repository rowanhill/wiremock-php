<?php

namespace WireMock\Client;

abstract class RequestJournalDependentResult extends PaginatedResult
{
    /** @var boolean */
    private $requestJournalDisabled;

    /**
     * @param Meta $meta
     * @param bool $requestJournalDisabled
     */
    public function __construct(Meta $meta, bool $requestJournalDisabled)
    {
        parent::__construct($meta);
        $this->requestJournalDisabled = $requestJournalDisabled;
    }

    /**
     * @return bool
     */
    public function isRequestJournalDisabled()
    {
        return $this->requestJournalDisabled;
    }
}