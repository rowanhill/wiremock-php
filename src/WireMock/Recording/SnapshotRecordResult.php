<?php

namespace WireMock\Recording;

use WireMock\Stubbing\StubMapping;

class SnapshotRecordResult
{
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

    /**
     * @param array $array
     * @return SnapshotRecordResult
     */
    public static function fromArray($array)
    {
        return new SnapshotRecordResult(
            isset($array['mappings']) ?
                array_map(function($m) { return StubMapping::fromArray($m); }, $array['mappings']) :
                null,
            isset($array['ids']) ? $array['ids'] : null
        );
    }
}