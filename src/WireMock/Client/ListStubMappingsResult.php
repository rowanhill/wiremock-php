<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Stubbing\StubMapping;

class ListStubMappingsResult extends PaginatedResult implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var StubMapping[] */
    private $mappings;

    /**
     * @param Meta $meta
     * @param StubMapping[] $_mappings
     */
    public function __construct(Meta $meta, array $_mappings)
    {
        parent::__construct($meta);
        $this->mappings = $_mappings;
    }


    /**
     * @return StubMapping[]
     */
    public function getMappings()
    {
        return $this->mappings;
    }
}