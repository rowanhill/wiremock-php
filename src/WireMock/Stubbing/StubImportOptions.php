<?php

namespace WireMock\Stubbing;

class StubImportOptions
{
    const OVERWRITE = 'OVERWRITE';
    const IGNORE = 'IGNORE';

    /** @var string */
    private $duplicatePolicy;
    /** @var boolean */
    private $deleteAllNotInImport;

    /**
     * StubImportOptions constructor.
     * @param string $duplicatePolicy
     * @param bool $deleteAllNotInImport
     */
    public function __construct($duplicatePolicy, $deleteAllNotInImport)
    {
        $this->duplicatePolicy = $duplicatePolicy;
        $this->deleteAllNotInImport = $deleteAllNotInImport;
    }

    /**
     * @return string
     */
    public function getDuplicatePolicy(): string
    {
        return $this->duplicatePolicy;
    }

    /**
     * @return bool
     */
    public function isDeleteAllNotInImport(): bool
    {
        return $this->deleteAllNotInImport;
    }
}