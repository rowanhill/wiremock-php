<?php

namespace WireMock\Client;

abstract class PaginatedResult
{
    /** @var Meta */
    private $_meta;
    /** @var array */
    protected $_list;

    /**
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->_meta = new Meta($array['meta']);
        $this->_list = $this->getList($array);
    }

    /**
     * @return Meta
     */
    public function getMeta()
    {
        return $this->_meta;
    }

    /**
     * Extract the list of paginated items from the top level results array
     * @param array $array
     * @return array
     */
    protected abstract function getList(array $array);
}

class Meta
{
    /** @var int */
    private $_total;

    /**
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->_total = $array['total'];
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->_total;
    }
}