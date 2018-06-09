<?php

namespace WireMock\Recording;

use WireMock\Client\RequestPatternBuilder;

class RecordSpecBuilder
{
    /** @var string */
    private $_targetBaseUrl;
    /** @var RequestPatternBuilder */
    private $_requestPatternBuilder;
    /** @var array */
    private $_captureHeaders = array();
    /** @var array */
    private $_extractBodyCriteria = array();
    /** @var bool */
    private $_persist = true;
    /** @var bool */
    private $_repeatsAsScenarios = true;
    /** @var string[] */
    private $_transformers = array();
    /** @var array */
    private $_transformerParameters = array();
    /** @var array */
    private $_requestBodyPattern = null;
    /** @var string */
    private $_format = null;

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
     * @param RequestPatternBuilder $requestPatternBuilder
     * @return RecordSpecBuilder
     */
    public function onlyRequestsMatching($requestPatternBuilder)
    {
        $this->_requestPatternBuilder = $requestPatternBuilder;
        return $this;
    }

    /**
     * @param string $name
     * @param bool $caseInsensitive
     * @return RecordSpecBuilder
     */
    public function captureHeader($name, $caseInsensitive = false)
    {
        $this->_captureHeaders[$name] = $caseInsensitive ?
            array('caseInsensitive' => true) :
            new \stdClass();
        return $this;
    }

    /**
     * @param int $bytes
     * @return RecordSpecBuilder
     */
    public function extractBinaryBodiesOver($bytes)
    {
        $this->_extractBodyCriteria['binarySizeThreshold'] = (string)$bytes;
        return $this;
    }

    /**
     * @param int $bytes
     * @return RecordSpecBuilder
     */
    public function extractTextBodiesOver($bytes)
    {
        $this->_extractBodyCriteria['textSizeThreshold'] = (string)$bytes;
        return $this;
    }

    /**
     * @param $persist
     * @return RecordSpecBuilder
     */
    public function makeStubsPersistent($persist)
    {
        $this->_persist = $persist;
        return $this;
    }

    /**
     * @return RecordSpecBuilder
     */
    public function ignoreRepeatRequests()
    {
        $this->_repeatsAsScenarios = false;
        return $this;
    }

    /**
     * Takes a variable number of transformer names.
     * 
     * @return RecordSpecBuilder
     */
    public function transformers()
    {
        $this->_transformers = func_get_args();
        return $this;
    }

    /**
     * @param $paramsArray
     * @return RecordSpecBuilder
     */
    public function transformerParameters($paramsArray)
    {
        $this->_transformerParameters = $paramsArray;
        return $this;
    }

    /**
     * @param boolean $ignoreArrayOrder
     * @param boolean $ignoreExtraElements
     * @return RecordSpecBuilder
     */
    public function matchRequestBodyWithEqualToJson($ignoreArrayOrder = null, $ignoreExtraElements = null)
    {
        $this->_requestBodyPattern = array(
            'matcher' => 'equalToJson'
        );
        if (!is_null($ignoreArrayOrder)) {
            $this->_requestBodyPattern['ignoreArrayOrder'] = $ignoreArrayOrder;
        }
        if (!is_null($ignoreExtraElements)) {
            $this->_requestBodyPattern['ignoreExtraElements'] = $ignoreExtraElements;
        }
        return $this;
    }

    /**
     * @return RecordSpecBuilder
     */
    public function matchRequestBodyWithEqualToXml()
    {
        $this->_requestBodyPattern = array(
            'matcher' => 'equalToXml'
        );
        return $this;
    }

    /**
     * @param boolean $caseInsensitive
     * @return RecordSpecBuilder
     */
    public function matchRequestBodyWithEqualTo($caseInsensitive = null)
    {
        $this->_requestBodyPattern = array(
            'matcher' => 'equalTo',
            'caseInsensitive' => $caseInsensitive
        );
        return $this;
    }

    /**
     * @param boolean $ignoreArrayOrder
     * @param boolean $ignoreExtraElements
     * @param boolean $caseInsensitive
     * @return RecordSpecBuilder
     */
    public function chooseBodyMatchTypeAutomatically($ignoreArrayOrder, $ignoreExtraElements, $caseInsensitive)
    {
        $this->_requestBodyPattern = array(
            'matcher' => 'auto',
            'caseInsensitive' => $caseInsensitive,
            'ignoreArrayOrder' => $ignoreArrayOrder,
            'ignoreExtraElements' => $ignoreExtraElements
        );
        return $this;
    }

    /**
     * @param string $format one of RecordSpec::FULL or RecordSpec::IDS
     * @return RecordSpecBuilder
     */
    public function withOutputFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * @return RecordSpec
     */
    public function build()
    {
        return new RecordSpec(
            $this->_targetBaseUrl,
            $this->_requestPatternBuilder ? $this->_requestPatternBuilder->build() : null,
            $this->_captureHeaders,
            $this->_extractBodyCriteria,
            $this->_persist,
            $this->_repeatsAsScenarios,
            $this->_transformers,
            $this->_transformerParameters,
            $this->_requestBodyPattern,
            $this->_format
        );
    }
}