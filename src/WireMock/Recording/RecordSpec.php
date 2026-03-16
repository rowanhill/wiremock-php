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
    /** @var array<int|string, mixed>|null */
    private $captureHeaders;
    /** @var array<int|string, mixed>|null */
    private $extractBodyCriteria;
    /** @var boolean */
    private $persist;
    /** @var boolean */
    private $repeatsAsScenarios;
    /** @var string[]|null */
    private $transformers;
    /** @var array<int|string, mixed>|null */
    private $transformerParameters;
    /** @var array<int|string, mixed>|null */
    private $requestBodyPattern;
    /** @var string|null */
    private $outputFormat;

    /**
     * @param string $targetBaseUrl
     * @param ProxiedServeEventFilters|null $filters
     * @param array<int|string, mixed>|null $captureHeaders
     * @param array<int|string, mixed>|null $extractBodyCriteria
     * @param boolean $persist
     * @param boolean $repeatsAsScenarios
     * @param string[]|null $transformers
     * @param array<int|string, mixed>|null $transformerParameters
     * @param array<int|string, mixed>|null $requestBodyPattern
     * @param string|null $outputFormat
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
     * @return array<int|string, mixed>|null
     */
    public function getCaptureHeaders(): ?array
    {
        return $this->captureHeaders;
    }

    /**
     * @return array<int|string, mixed>|null
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
     * @return string[]|null
     */
    public function getTransformers(): ?array
    {
        return $this->transformers;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getTransformerParameters(): ?array
    {
        return $this->transformerParameters;
    }

    /**
     * @return array<int|string, mixed>
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