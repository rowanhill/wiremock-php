<?php

namespace WireMock\Client;

use WireMock\Stubbing\StubMapping;

class ListStubMappingsResult extends PaginatedResult
{
    /** @var StubMapping[] */
    private $mappings;

    /**
     * @param Meta $meta
     * @param StubMapping[] $mappings
     */
    public function __construct(Meta $meta, array $mappings)
    {
        parent::__construct($meta);
        $this->mappings = $mappings;
    }


    /**
     * @return StubMapping[]
     */
    public function getMappings()
    {
        return $this->mappings;
    }
}