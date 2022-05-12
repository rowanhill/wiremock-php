<?php

namespace WireMock\Client;

class JsonValueMatchingStrategy extends ValueMatchingStrategy
{
    /** @var boolean|null */
    private $ignoreArrayOrder = null;
    /** @var boolean|null  */
    private $ignoreExtraElements = null;

    public function __construct($matchingValue, $ignoreArrayOrder = null, $ignoreExtraElements = null)
    {
        parent::__construct('equalToJson', $matchingValue);
        $this->ignoreArrayOrder = $ignoreArrayOrder;
        $this->ignoreExtraElements = $ignoreExtraElements;
    }

    /**
     * @return bool|null
     */
    public function getIgnoreArrayOrder()
    {
        return $this->ignoreArrayOrder;
    }

    /**
     * @return bool|null
     */
    public function getIgnoreExtraElements()
    {
        return $this->ignoreExtraElements;
    }
}
