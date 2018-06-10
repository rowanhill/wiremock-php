<?php

namespace WireMock\Client;

abstract class RequestJournalDependentResult extends PaginatedResult
{
    /** @var boolean */
    private $_requestJournalDisabled;

    /**
     * @param array $array
     */
    public function __construct(array $array)
    {
        parent::__construct($array);
        $this->_requestJournalDisabled = $array['requestJournalDisabled'];
    }
}