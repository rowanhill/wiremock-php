<?php

namespace WireMock\Client;

use WireMock\Stubbing\StubMapping;

class ListStubMappingsResult extends PaginatedResult
{
    /**
     * @param array $array
     * @return StubMapping[]
     */
    protected function getList(array $array)
    {
        return array_map(function($sm) { return StubMapping::fromArray($sm); }, $array['mappings']);
    }

    /**
     * @return StubMapping[]
     */
    public function getMappings()
    {
        return $this->_list;
    }
}