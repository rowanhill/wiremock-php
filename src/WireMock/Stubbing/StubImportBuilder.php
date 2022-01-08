<?php

namespace WireMock\Stubbing;

use WireMock\Client\MappingBuilder;

class StubImportBuilder
{
    /** @var StubMapping[] */
    private $mappings = array();
    /** @var string */
    private $duplicatePolicy = StubImportOptions::OVERWRITE;
    /** @var boolean */
    private $deleteAllNotInImport = false;

    /**
     * @param StubMapping|MappingBuilder $mappingOrBuilder
     * @return StubImportBuilder
     * @throws \Exception If MappingBuilder is passed, and building the mapping throws an exception
     */
    public function stub($mappingOrBuilder)
    {
        $mapping = ($mappingOrBuilder instanceof  StubMapping) ? $mappingOrBuilder : $mappingOrBuilder->build();
        $this->mappings[] = $mapping;
        return $this;
    }

    /**
     * @return StubImportBuilder
     */
    public function ignoreExisting()
    {
        $this->duplicatePolicy = StubImportOptions::IGNORE;
        return $this;
    }

    /**
     * @return StubImportBuilder
     */
    public function overwriteExisting()
    {
        $this->duplicatePolicy = StubImportOptions::OVERWRITE;
        return $this;
    }

    /**
     * @return StubImportBuilder
     */
    public function deleteAllExistingStubsNotInImport()
    {
        $this->deleteAllNotInImport = true;
        return $this;
    }

    /**
     * @return StubImportBuilder
     */
    public function doNotDeleteExistingStubs()
    {
        $this->deleteAllNotInImport = false;
        return $this;
    }

    /**
     * @return StubImport
     */
    public function build()
    {
        return new StubImport(
            $this->mappings,
            new StubImportOptions($this->duplicatePolicy, $this->deleteAllNotInImport)
        );
    }
}