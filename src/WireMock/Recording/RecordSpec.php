<?php

namespace WireMock\Recording;

use WireMock\Matching\RequestPattern;

class RecordSpec
{
    /** @var string */
    private $_targetBaseUrl;
    /** @var RequestPattern */
    private $_requestPattern;
    /** @var array */
    private $_captureHeaders;
    /** @var array */
    private $_extractBodyCriteria;
    /** @var boolean */
    private $_persist;
    /** @var boolean */
    private $_repeatsAsScenarios;
    /** @var string[] */
    private $_transformers;
    /** @var array */
    private $_transformerParameters = array();
    /** @var array */
    private $_requestBodyPattern;

    /**
     * @param string $targetBaseUrl
     * @param RequestPattern $requestPattern
     * @param array $captureHeaders
     * @param array $extractBodyCriteria
     * @param boolean $persist
     * @param boolean $repeatsAsScenarios
     * @param string[] $transformers
     * @param array $transformerParameters
     * @param array $requestBodyPattern
     */
    public function __construct(
        $targetBaseUrl,
        $requestPattern,
        $captureHeaders,
        $extractBodyCriteria,
        $persist,
        $repeatsAsScenarios,
        $transformers,
        $transformerParameters,
        $requestBodyPattern
    ) {
        $this->_targetBaseUrl = $targetBaseUrl;
        $this->_requestPattern = $requestPattern;
        $this->_captureHeaders = $captureHeaders;
        $this->_extractBodyCriteria = $extractBodyCriteria;
        $this->_persist = $persist;
        $this->_repeatsAsScenarios = $repeatsAsScenarios;
        $this->_transformers = $transformers;
        $this->_transformerParameters = $transformerParameters;
        $this->_requestBodyPattern = $requestBodyPattern;
    }

    public function toArray()
    {
        $array = array(
            'targetBaseUrl' => $this->_targetBaseUrl
        );
        if ($this->_requestPattern) {
            $array['filters'] = $this->_requestPattern->toArray();
        }
        if ($this->_captureHeaders) {
            $array['captureHeaders'] = $this->_captureHeaders;
        }
        if ($this->_extractBodyCriteria) {
            $array['extractBodyCriteria'] = $this->_extractBodyCriteria;
        }
        if ($this->_persist != true) {
            $array['persist'] = $this->_persist;
        }
        if ($this->_repeatsAsScenarios != true) {
            $array['repeatsAsScenarios'] = $this->_repeatsAsScenarios;
        }
        if (!empty($this->_transformers)) {
            $array['transformers'] = $this->_transformers;
        }
        if (!empty($this->_transformerParameters)) {
            $array['transformerParameters'] = $this->_transformerParameters;
        }
        if ($this->_requestBodyPattern) {
            $array['requestBodyPattern'] = $this->_requestBodyPattern;
        }
        return $array;
    }
}