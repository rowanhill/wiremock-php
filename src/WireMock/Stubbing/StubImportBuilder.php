<?php

namespace WireMock\Stubbing;

use WireMock\Client\MappingBuilder;

class StubImportBuilder
{
    /** @var StubMapping[] */
    private $_mappings = array();
    /** @var string */
    private $_duplicatePolicy = StubImportOptions::OVERWRITE;
    /** @var boolean */
    private $_deleteAllNotInImport = false;

    /**
     * @param StubMapping|MappingBuilder $mappingOrBuilder
     * @return StubImportBuilder
     * @throws \Exception If MappingBuilder is passed, and building the mapping throws an exception
     */
    public function stub($mappingOrBuilder)
    {
        $mapping = ($mappingOrBuilder instanceof  StubMapping) ? $mappingOrBuilder : $mappingOrBuilder->build();
        $this->_mappings[] = $mapping;
        return $this;
    }

    /**
     * @return StubImportBuilder
     */
    public function ignoreExisting()
    {
        $this->_duplicatePolicy = StubImportOptions::IGNORE;
        return $this;
    }

    /**
     * @return StubImportBuilder
     */
    public function overwriteExisting()
    {
        $this->_duplicatePolicy = StubImportOptions::OVERWRITE;
        return $this;
    }

    /**
     * @return StubImportBuilder
     */
    public function deleteAllExistingStubsNotInImport()
    {
        $this->_deleteAllNotInImport = true;
        return $this;
    }

    /**
     * @return StubImportBuilder
     */
    public function doNotDeleteExistingStubs()
    {
        $this->_deleteAllNotInImport = false;
        return $this;
    }

    /**
     * @return StubImport
     */
    public function build()
    {
        return new StubImport(
            $this->_mappings,
            new StubImportOptions($this->_duplicatePolicy, $this->_deleteAllNotInImport)
        );
    }
}