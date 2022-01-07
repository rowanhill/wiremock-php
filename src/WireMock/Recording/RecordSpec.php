<?php

namespace WireMock\Recording;

use WireMock\Matching\RequestPattern;
use WireMock\Serde\PostNormalizationAmenderInterface;

class RecordSpec implements PostNormalizationAmenderInterface
{
    const FULL = 'FULL';
    const IDS = 'IDS';

    /** @var string */
    private $_targetBaseUrl;
    /** @var RequestPattern */
    private $_requestPattern;
    /** @var string[] */
    private $_requestIds;
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
    /** @var string */
    private $_outputFormat;
    /** @var boolean */
    private $_allowNonProxied;

    /**
     * @param string $targetBaseUrl
     * @param RequestPattern $requestPattern
     * @param string[] $requestIds
     * @param array $captureHeaders
     * @param array $extractBodyCriteria
     * @param boolean $persist
     * @param boolean $repeatsAsScenarios
     * @param string[] $transformers
     * @param array $transformerParameters
     * @param array $requestBodyPattern
     * @param string $format
     * @param boolean $allowNonProxied
     */
    public function __construct(
        $targetBaseUrl,
        $requestPattern,
        $requestIds,
        $captureHeaders,
        $extractBodyCriteria,
        $persist,
        $repeatsAsScenarios,
        $transformers,
        $transformerParameters,
        $requestBodyPattern,
        $format,
        $allowNonProxied
    ) {
        $this->_targetBaseUrl = $targetBaseUrl;
        $this->_requestPattern = $requestPattern;
        $this->_requestIds = $requestIds;
        $this->_captureHeaders = $captureHeaders;
        $this->_extractBodyCriteria = $extractBodyCriteria;
        $this->_persist = $persist;
        $this->_repeatsAsScenarios = $repeatsAsScenarios;
        $this->_transformers = $transformers;
        $this->_transformerParameters = $transformerParameters;
        $this->_requestBodyPattern = $requestBodyPattern;
        $this->_outputFormat = $format;
        $this->_allowNonProxied = $allowNonProxied;
    }

    public function toArray()
    {
        $array = array();
        if ($this->_targetBaseUrl) {
            $array['targetBaseUrl'] = $this->_targetBaseUrl;
        }
        if ($this->_requestPattern || $this->_requestIds || $this->_allowNonProxied) {
            $filters = array();
            if ($this->_requestPattern) {
                $filters = array_merge($filters, $this->_requestPattern->toArray());
            }
            if ($this->_requestIds) {
                $filters = array_merge($filters, array('ids' => $this->_requestIds));
            }
            if ($this->_allowNonProxied) {
                $filters = array_merge($filters, array('allowNonProxied' => true));
            }
            $array['filters'] = $filters;
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
        if ($this->_outputFormat) {
            $array['outputFormat'] = $this->_outputFormat;
        }
        return !empty($array) ? $array : new \stdClass();
    }

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        if (isset($normalisedArray['requestPattern']) || isset($normalisedArray['requestIds']) || isset($normalisedArray['allowNonProxied'])) {
            $filters = array();
            if (isset($normalisedArray['requestPattern'])) {
                $filters = array_merge($filters, $normalisedArray['requestPattern']);
                unset($normalisedArray['requestPattern']);
            }
            if (isset($normalisedArray['requestIds'])) {
                $filters = array_merge($filters, array('ids' => $normalisedArray['requestIds']));
                unset($normalisedArray['requestIds']);
            }
            if (isset($normalisedArray['allowNonProxied'])) {
                $filters = array_merge($filters, array('allowNonProxied' => true));
                unset($normalisedArray['allowNonProxied']);
            }
            $normalisedArray['filters'] = $filters;
        }
        return $normalisedArray;
    }
}