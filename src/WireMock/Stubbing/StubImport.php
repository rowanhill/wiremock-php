<?php

namespace WireMock\Stubbing;

class StubImport
{
    /** @var StubMapping[] */
    private $mappings;
    /** @var StubImportOptions */
    private $importOptions;

    /**
     * StubImport constructor.
     * @param StubMapping[] $_mappings
     * @param StubImportOptions $_importOptions
     */
    public function __construct(array $_mappings, StubImportOptions $_importOptions)
    {
        $this->mappings = $_mappings;
        $this->importOptions = $_importOptions;
    }

    /**
     * @return StubMapping[]
     */
    public function getMappings(): array
    {
        return $this->mappings;
    }

    /**
     * @return StubImportOptions
     */
    public function getImportOptions(): StubImportOptions
    {
        return $this->importOptions;
    }
}