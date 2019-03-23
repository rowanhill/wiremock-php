<?php

namespace WireMock\Stubbing;

class StubImport
{
    /** @var StubMapping[] */
    private $_mappings;
    /** @var StubImportOptions */
    private $_importOptions;

    /**
     * StubImport constructor.
     * @param StubMapping[] $_mappings
     * @param StubImportOptions $_importOptions
     */
    public function __construct(array $_mappings, StubImportOptions $_importOptions)
    {
        $this->_mappings = $_mappings;
        $this->_importOptions = $_importOptions;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'mappings' => array_map(function(/**@var $m StubMapping */$m) { return $m->toArray(); }, $this->_mappings),
            'importOptions' => $this->_importOptions->toArray()
        );
    }
}