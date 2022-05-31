<?php

namespace WireMock\Recording;

class RecordSpec
{
    const FULL = 'FULL';
    const IDS = 'IDS';

    /** @var string */
    private $targetBaseUrl;
    /** @var ProxiedServeEventFilters|null */
    private $filters;
    /** @var array */
    private $captureHeaders;
    /** @var array */
    private $extractBodyCriteria;
    /** @var boolean */
    private $persist;
    /** @var boolean */
    private $repeatsAsScenarios;
    /** @var string[] */
    private $transformers;
    /** @var array */
    private $transformerParameters = array();
    /** @var array */
    private $requestBodyPattern;
    /** @var string */
    private $outputFormat;

    /**
     * @param string $targetBaseUrl
     * @param ProxiedServeEventFilters $filters
     * @param array $captureHeaders
     * @param array $extractBodyCriteria
     * @param boolean $persist
     * @param boolean $repeatsAsScenarios
     * @param string[] $transformers
     * @param array $transformerParameters
     * @param array $requestBodyPattern
     * @param string $outputFormat
     */
    public function __construct(
        $targetBaseUrl,
        $filters,
        $captureHeaders,
        $extractBodyCriteria,
        $persist,
        $repeatsAsScenarios,
        $transformers,
        $transformerParameters,
        $requestBodyPattern,
        $outputFormat
    ) {
        $this->targetBaseUrl = $targetBaseUrl;
        $this->filters = $filters;
        $this->captureHeaders = $captureHeaders;
        $this->extractBodyCriteria = $extractBodyCriteria;
        $this->persist = $persist;
        $this->repeatsAsScenarios = $repeatsAsScenarios;
        $this->transformers = $transformers;
        $this->transformerParameters = $transformerParameters;
        $this->requestBodyPattern = $requestBodyPattern;
        $this->outputFormat = $outputFormat;
    }

    /**
     * @return string
     */
    public function getTargetBaseUrl(): ?string
    {
        return $this->targetBaseUrl;
    }

    /**
     * @return ProxiedServeEventFilters|null
     */
    public function getFilters(): ?ProxiedServeEventFilters
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getCaptureHeaders(): ?array
    {
        return $this->captureHeaders;
    }

    /**
     * @return array
     */
    public function getExtractBodyCriteria(): ?array
    {
        return $this->extractBodyCriteria;
    }

    /**
     * @return bool
     */
    public function isPersist(): bool
    {
        return $this->persist;
    }

    /**
     * @return bool
     */
    public function isRepeatsAsScenarios(): bool
    {
        return $this->repeatsAsScenarios;
    }

    /**
     * @return string[]
     */
    public function getTransformers(): ?array
    {
        return $this->transformers;
    }

    /**
     * @return array
     */
    public function getTransformerParameters(): ?array
    {
        return $this->transformerParameters;
    }

    /**
     * @return array
     */
    public function getRequestBodyPattern(): ?array
    {
        return $this->requestBodyPattern;
    }

    /**
     * @return string
     */
    public function getOutputFormat(): ?string
    {
        return $this->outputFormat;
    }
}