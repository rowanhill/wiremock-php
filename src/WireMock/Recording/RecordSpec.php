<?php

namespace WireMock\Recording;

use WireMock\Matching\RequestPattern;
use WireMock\Serde\PostNormalizationAmenderInterface;

class RecordSpec implements PostNormalizationAmenderInterface
{
    const FULL = 'FULL';
    const IDS = 'IDS';

    /** @var string */
    private $targetBaseUrl;
    /** @var RequestPattern */
    private $requestPattern;
    /** @var string[] */
    private $requestIds;
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
    /** @var boolean */
    private $allowNonProxied;

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
        $this->targetBaseUrl = $targetBaseUrl;
        $this->requestPattern = $requestPattern;
        $this->requestIds = $requestIds;
        $this->captureHeaders = $captureHeaders;
        $this->extractBodyCriteria = $extractBodyCriteria;
        $this->persist = $persist;
        $this->repeatsAsScenarios = $repeatsAsScenarios;
        $this->transformers = $transformers;
        $this->transformerParameters = $transformerParameters;
        $this->requestBodyPattern = $requestBodyPattern;
        $this->outputFormat = $format;
        $this->allowNonProxied = $allowNonProxied;
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