<?php

namespace WireMock\Recording;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Stubbing\StubMapping;

class SnapshotRecordResult implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;
    
    /** @var StubMapping[] */
    private $_mappings;
    /** @var string[] */
    private $_ids;

    /**
     * @param StubMapping[] $mappings
     * @param string[] $ids
     */
    public function __construct($mappings, $ids)
    {
        $this->_mappings = $mappings;
        $this->_ids = $ids;
    }

    /**
     * @return StubMapping[]
     */
    public function getMappings()
    {
        return $this->_mappings;
    }

    /**
     * @return string[]
     */
    public function getIds()
    {
        return $this->_ids;
    }
}