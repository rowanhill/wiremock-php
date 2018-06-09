<?php

namespace WireMock\Recording;

class RecordSpecBuilder
{
    /** @var string */
    private $_targetBaseUrl;

    /**
     * @param $targetBaseUrl
     * @return RecordSpecBuilder
     */
    public function forTarget($targetBaseUrl)
    {
        $this->_targetBaseUrl = $targetBaseUrl;
        return $this;
    }

    /**
     * @return RecordSpec
     */
    public function build()
    {
        return new RecordSpec(
            $this->_targetBaseUrl
        );
    }
}