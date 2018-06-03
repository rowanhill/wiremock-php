<?php

namespace WireMock\Client;

class GetServeEventsResult extends RequestJournalDependentResult
{
    public function __construct(array $array)
    {
        parent::__construct($array);
    }

    /**
     * @param array $array
     * @return ServeEvent[]
     */
    protected function getList(array $array)
    {
        return array_map(function($r) { return ServeEvent::fromArray($r); }, $array['requests']);
    }

    /**
     * @return ServeEvent[]
     */
    public function getRequests()
    {
        return $this->_list;
    }
}