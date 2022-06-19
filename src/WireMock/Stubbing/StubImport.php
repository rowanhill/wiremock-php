<?php

namespace WireMock\Stubbing;

class StubImport
{
    /** @var StubMapping[]|null */
    private $mappings;
    /** @var StubImportOptions */
    private $importOptions;

    /**
     * StubImport constructor.
     * @param StubMapping[]|null $mappings
     * @param StubImportOptions $importOptions
     */
    public function __construct($mappings, StubImportOptions $importOptions)
    {
        $this->mappings = $mappings;
        $this->importOptions = $importOptions;
    }

    /**
     * @return StubMapping[]|null
     */
    public function getMappings(): ?array
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