<?php

namespace WireMock\Recording;

use WireMock\Stubbing\StubMapping;

class SnapshotRecordResult
{
    /** @var StubMapping[] */
    private $_mappings;

    /**
     * @param StubMapping[] $mappings
     */
    public function __construct($mappings)
    {
        $this->_mappings = $mappings;
    }

    /**
     * @return StubMapping[]
     */
    public function getMappings()
    {
        return $this->_mappings;
    }

    /**
     * @param array $array
     * @return SnapshotRecordResult
     */
    public static function fromArray($array)
    {
        return new SnapshotRecordResult(
            array_map(function($m) { return StubMapping::fromArray($m); }, $array['mappings'])
        );
    }
}