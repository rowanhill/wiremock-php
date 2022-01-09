<?php

namespace WireMock\Recording;

use WireMock\Stubbing\StubMapping;

class SnapshotRecordResult
{
    /** @var StubMapping[] */
    private $mappings;
    /** @var string[] */
    private $ids;

    /**
     * @param StubMapping[] $mappings
     * @param string[] $ids
     */
    public function __construct($mappings = [], $ids = [])
    {
        $this->mappings = $mappings;
        $this->ids = $ids;
    }

    /**
     * @return StubMapping[]
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @return string[]
     */
    public function getIds()
    {
        return $this->ids;
    }
}