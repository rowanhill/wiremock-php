<?php

namespace WireMock\Stubbing;

class StubImportOptions
{
    const OVERWRITE = 'OVERWRITE';
    const IGNORE = 'IGNORE';

    /** @var string */
    private $_duplicatePolicy;
    /** @var boolean */
    private $_deleteAllNotInImport;

    /**
     * StubImportOptions constructor.
     * @param string $_duplicatePolicy
     * @param bool $_deleteAllNotInImport
     */
    public function __construct($_duplicatePolicy, $_deleteAllNotInImport)
    {
        $this->_duplicatePolicy = $_duplicatePolicy;
        $this->_deleteAllNotInImport = $_deleteAllNotInImport;
    }
}