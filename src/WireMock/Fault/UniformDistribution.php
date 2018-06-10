<?php

namespace WireMock\Fault;

class UniformDistribution implements DelayDistribution
{
    /** @var int */
    private $_lower;
    /** @var int */
    private $_upper;

    /**
     * @param int $lower
     * @param int $upper
     */
    public function __construct($lower, $upper)
    {
        $this->_lower = $lower;
        $this->_upper = $upper;
    }

    /**
     * @return int
     */
    public function getLower()
    {
        return $this->_lower;
    }

    /**
     * @return int
     */
    public function getUpper()
    {
        return $this->_upper;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array (
            'type' => 'uniform',
            'lower' => $this->_lower,
            'upper' => $this->_upper
        );
    }

    /**
     * @param array $array
     * @return UniformDistribution
     */
    public static function fromArray(array $array)
    {
        return new UniformDistribution($array['lower'], $array['upper']);
    }
}