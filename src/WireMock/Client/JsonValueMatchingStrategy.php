<?php

namespace WireMock\Client;

class JsonValueMatchingStrategy extends ValueMatchingStrategy
{
    /** @var boolean|null */
    private $ignoreArrayOrder;
    /** @var boolean|null  */
    private $ignoreExtraElements;

    /**
     * @param mixed $matchingValue
     * @param bool|null $ignoreArrayOrder
     * @param bool|null $ignoreExtraElements  
     */
    public function __construct($matchingValue, $ignoreArrayOrder = null, $ignoreExtraElements = null)
    {
        parent::__construct('equalToJson', $matchingValue);
        $this->ignoreArrayOrder = $ignoreArrayOrder;
        $this->ignoreExtraElements = $ignoreExtraElements;
    }

    /**
     * @return boolean|null
     */
    public function getIgnoreArrayOrder()
    {
        return $this->ignoreArrayOrder;
    }

    /**
     * @return boolean|null
     */
    public function getIgnoreExtraElements()
    {
        return $this->ignoreExtraElements;
    }
}
