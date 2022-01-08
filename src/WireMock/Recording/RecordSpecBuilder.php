<?php

namespace WireMock\Recording;

use WireMock\Client\RequestPatternBuilder;

class RecordSpecBuilder
{
    /** @var string */
    private $targetBaseUrl;
    /** @var RequestPatternBuilder */
    private $requestPatternBuilder;
    /** @var string[] */
    private $requestIds;
    /** @var array */
    private $captureHeaders = array();
    /** @var array */
    private $extractBodyCriteria = array();
    /** @var bool */
    private $persist = true;
    /** @var bool */
    private $repeatsAsScenarios = true;
    /** @var string[] */
    private $transformers = array();
    /** @var array */
    private $transformerParameters = array();
    /** @var array */
    private $requestBodyPattern = null;
    /** @var string */
    private $format = null;
    /** @var boolean */
    private $allowNonProxied = null;

    /**
     * @param $targetBaseUrl
     * @return RecordSpecBuilder
     */
    public function forTarget($targetBaseUrl)
    {
        $this->targetBaseUrl = $targetBaseUrl;
        return $this;
    }

    /**
     * @param RequestPatternBuilder $requestPatternBuilder
     * @return RecordSpecBuilder
     */
    public function onlyRequestsMatching($requestPatternBuilder)
    {
        $this->requestPatternBuilder = $requestPatternBuilder;
        return $this;
    }

    /**
     * @param string[] $ids
     * @return RecordSpecBuilder
     */
    public function onlyRequestIds(array $ids)
    {
        $this->requestIds = $ids;
        return $this;
    }

    /**
     * @param string $name
     * @param bool $caseInsensitive
     * @return RecordSpecBuilder
     */
    public function captureHeader($name, $caseInsensitive = false)
    {
        $this->captureHeaders[$name] = $caseInsensitive ?
            array('caseInsensitive' => true) :
            [];
        return $this;
    }

    /**
     * @param int $bytes
     * @return RecordSpecBuilder
     */
    public function extractBinaryBodiesOver($bytes)
    {
        $this->extractBodyCriteria['binarySizeThreshold'] = (string)$bytes;
        return $this;
    }

    /**
     * @param int $bytes
     * @return RecordSpecBuilder
     */
    public function extractTextBodiesOver($bytes)
    {
        $this->extractBodyCriteria['textSizeThreshold'] = (string)$bytes;
        return $this;
    }

    /**
     * @param $persist
     * @return RecordSpecBuilder
     */
    public function makeStubsPersistent($persist)
    {
        $this->persist = $persist;
        return $this;
    }

    /**
     * @return RecordSpecBuilder
     */
    public function ignoreRepeatRequests()
    {
        $this->repeatsAsScenarios = false;
        return $this;
    }

    /**
     * Takes a variable number of transformer names.
     * 
     * @return RecordSpecBuilder
     */
    public function transformers()
    {
        $this->transformers = func_get_args();
        return $this;
    }

    /**
     * @param $paramsArray
     * @return RecordSpecBuilder
     */
    public function transformerParameters($paramsArray)
    {
        $this->transformerParameters = $paramsArray;
        return $this;
    }

    /**
     * @param boolean $ignoreArrayOrder
     * @param boolean $ignoreExtraElements
     * @return RecordSpecBuilder
     */
    public function matchRequestBodyWithEqualToJson($ignoreArrayOrder = null, $ignoreExtraElements = null)
    {
        $this->requestBodyPattern = array(
            'matcher' => 'equalToJson'
        );
        if (!is_null($ignoreArrayOrder)) {
            $this->requestBodyPattern['ignoreArrayOrder'] = $ignoreArrayOrder;
        }
        if (!is_null($ignoreExtraElements)) {
            $this->requestBodyPattern['ignoreExtraElements'] = $ignoreExtraElements;
        }
        return $this;
    }

    /**
     * @return RecordSpecBuilder
     */
    public function matchRequestBodyWithEqualToXml()
    {
        $this->requestBodyPattern = array(
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
        $this->requestBodyPattern = array(
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
        $this->requestBodyPattern = array(
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
        $this->format = $format;
        return $this;
    }

    /**
     * @param boolean $allow
     * @return RecordSpecBuilder
     */
    public function allowNonProxied($allow)
    {
        $this->allowNonProxied = $allow;
        return $this;
    }

    /**
     * @return RecordSpec
     */
    public function build()
    {
        return new RecordSpec(
            $this->targetBaseUrl,
            $this->requestPatternBuilder ? $this->requestPatternBuilder->build() : null,
            $this->requestIds,
            $this->captureHeaders,
            $this->extractBodyCriteria,
            $this->persist,
            $this->repeatsAsScenarios,
            $this->transformers,
            $this->transformerParameters,
            $this->requestBodyPattern,
            $this->format,
            $this->allowNonProxied
        );
    }
}