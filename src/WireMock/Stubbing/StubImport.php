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
     * @param StubMapping[] $mappings
     * @param StubImportOptions $importOptions
     */
    public function __construct(array $mappings, StubImportOptions $importOptions)
    {
        $this->mappings = $mappings;
        $this->importOptions = $importOptions;
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