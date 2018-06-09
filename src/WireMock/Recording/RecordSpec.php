<?php

namespace WireMock\Recording;

class RecordSpec
{
    /** @var string */
    private $_targetBaseUrl;

    /**
     * @param string $targetBaseUrl
     */
    public function __construct($targetBaseUrl)
    {
        $this->_targetBaseUrl = $targetBaseUrl;
    }

    public function toArray()
    {
        return array(
            'targetBaseUrl' => $this->_targetBaseUrl
        );
    }
}