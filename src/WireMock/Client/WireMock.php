<?php

namespace WireMock\Client;

use DateTime;
use http\Client\Request;
use http\Url;
use WireMock\Fault\DelayDistribution;
use WireMock\Fault\GlobalDelaySettings;
use WireMock\Matching\UrlMatchingStrategy;
use WireMock\PostServe\WebhookDefinition;
use WireMock\Recording\RecordingStatusResult;
use WireMock\Recording\RecordSpecBuilder;
use WireMock\Recording\SnapshotRecordResult;
use WireMock\Serde\Serializer;
use WireMock\Serde\SerializerFactory;
use WireMock\Stubbing\StubImport;
use WireMock\Stubbing\StubImportBuilder;
use WireMock\Stubbing\StubMapping;
use WireMock\Verification\CountMatchingStrategy;

class WireMock
{
    /** @var string */
    private $hostname;
    /** @var int */
    private $port;
    private HttpWait $httpWait;
    private Curl $curl;
    private Serializer $serializer;

    /**
     * @param string $hostname
     * @param int $port
     */
    public static function create($hostname = 'localhost', $port = 8080): self
    {
        $httpWait = new HttpWait();
        $curl = new Curl();

        $serializer = SerializerFactory::default();
        return new self($httpWait, $curl, $serializer, $hostname, $port);
    }

    /**
     * @param string $hostname
     * @param int $port
     */
    public function __construct(
        HttpWait $httpWait,
        Curl $curl,
        Serializer $serializer,
        $hostname = 'localhost',
        $port = 8080
    ) {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->httpWait = $httpWait;
        $this->curl = $curl;
        $this->serializer = $serializer;
    }

    /**
     * @param boolean $debug
     * @param int $timeoutSecs
     */
    public function isAlive($timeoutSecs = 10, $debug = true): bool
    {
        $url = $this->_makeUrl('__admin/');
        return $this->httpWait->waitForServerToGive200($url, $timeoutSecs, $debug);
    }

    public function isShutDown(): bool
    {
        $url = $this->_makeUrl('__admin/');
        return $this->httpWait->waitForServerToFailToRespond($url);
    }

    public function stubFor(MappingBuilder $mappingBuilder): StubMapping
    {
        $stubMapping = $mappingBuilder->build();
        /** @var StubMapping $responseMapping */
        $responseMapping = $this->doPost('__admin/mappings', $stubMapping, StubMapping::class);
        $stubMapping->setId($responseMapping->getId());
        return $stubMapping;
    }

    public function editStub(MappingBuilder $mappingBuilder): StubMapping
    {
        $stubMapping = $mappingBuilder->build();
        if (!$stubMapping->getId()) {
            throw new VerificationException('Cannot edit a stub without an id');
        }
        $this->doPut('__admin/mappings/' . urlencode($stubMapping->getId()), $stubMapping);
        return $stubMapping;
    }

    /**
     * @param StubImport|StubImportBuilder $stubImportsOrBuilder
     */
    public function importStubs($stubImportsOrBuilder): void
    {
        $stubImports = ($stubImportsOrBuilder instanceof StubImport) ?
            $stubImportsOrBuilder :
            $stubImportsOrBuilder->build();
        $this->doPost('__admin/mappings/import', $stubImports);
    }

    /**
     * @param RequestPatternBuilder|CountMatchingStrategy|int $requestPatternBuilderOrCount
     * @param RequestPatternBuilder|null $requestPatternBuilder
     * @throws ClientException
     * @throws VerificationException
     */
    public function verify($requestPatternBuilderOrCount, ?RequestPatternBuilder $requestPatternBuilder = null): void
    {
        if ($requestPatternBuilderOrCount instanceof CountMatchingStrategy) {
            $patternBuilder = $requestPatternBuilder;
            $numberOfRequestsMatcher = $requestPatternBuilderOrCount;
        } else if (is_int($requestPatternBuilderOrCount)) {
            $patternBuilder = $requestPatternBuilder;
            $numberOfRequestsMatcher = self::exactly($requestPatternBuilderOrCount);
        } else {
            $patternBuilder = $requestPatternBuilderOrCount;
            $numberOfRequestsMatcher = null;
        }

        $requestPattern = $patternBuilder->build(); // @phpstan-ignore method.nonObject (@TODO this looks like an actual bug to me?)
        $response = $this->doPost('__admin/requests/count', $requestPattern, CountMatchingRequestsResult::class);
        $count = $response->getCount();

        if ($numberOfRequestsMatcher === null) {
            // If $numberOfRequestsMatcher is not specified, any non-zero number of requests is acceptable
            if ($count < 1) {
                throw new VerificationException("Expected at least one request, but found $count");
            }
        } else {
            if (!$numberOfRequestsMatcher->matches($count)) {
                $describe = $numberOfRequestsMatcher->describe();
                throw new VerificationException("Expected $describe request(s), but found $count");
            }
        }
    }

    /**
     * getAllServeEvents can be called in a variety of ways:
     * - with no parameters, to return all serve events
     * - with an optional DateTime as the first param and an optional int as the second param to specify
     *   since and limit, for pagination
     * - with a ServeEventQuery (since 2.32) which provides additional controls
     *
     * @param ServeEventQuery|DateTime|null $sinceOrQuery
     * @param int|null $limit
     * @return GetServeEventsResult
     */
    public function getAllServeEvents($sinceOrQuery = null, $limit = null)
    {
        $pathAndParams = '__admin/requests';

        if ($sinceOrQuery instanceof ServeEventQuery) {
            $query = $sinceOrQuery;
        } else {
            $query = ServeEventQuery::paginated($sinceOrQuery, $limit);
        }

        $params = $query->toParamsString();
        if ($params != '') {
            $pathAndParams .= '?' . $params;
        }

        return $this->doGet($pathAndParams, GetServeEventsResult::class);
    }

    /**
     * @param RequestPatternBuilder $requestPatternBuilder
     * @return LoggedRequest[]
     */
    public function findAll(RequestPatternBuilder $requestPatternBuilder)
    {
        $requestPattern = $requestPatternBuilder->build();
        $result = $this->doPost('__admin/requests/find', $requestPattern, FindRequestsResult::class);
        return $result->getRequests();
    }

    /**
     * @return UnmatchedRequests
     */
    public function findUnmatchedRequests()
    {
        return $this->doGet('__admin/requests/unmatched', UnmatchedRequests::class);
    }

    /**
     * @param LoggedRequest|RequestPatternBuilder $loggedRequestOrPattern
     * @return FindNearMissesResult
     * @throws \Exception
     */
    public function findNearMissesFor($loggedRequestOrPattern)
    {
        if ($loggedRequestOrPattern instanceof LoggedRequest) {
            $path = '__admin/near-misses/request';
        } elseif ($loggedRequestOrPattern instanceof RequestPatternBuilder) {
            $loggedRequestOrPattern = $loggedRequestOrPattern->build();
            $path = '__admin/near-misses/request-pattern';
        } else {
            throw new \Exception('Unexpected near miss specifier: ' . print_r($loggedRequestOrPattern, true));
        }

        return $this->doPost($path, $loggedRequestOrPattern, FindNearMissesResult::class);
    }

    /**
     * @return FindNearMissesResult
     */
    public function findNearMissesForAllUnmatched()
    {
        return $this->doGet('__admin/requests/unmatched/near-misses', FindNearMissesResult::class);
    }

    /**
     * Deletes all serve events from the WireMock server's request journal
     */
    public function resetAllRequests(): void
    {
        $this->doDelete('__admin/requests');
    }

    /**
     * Removes specified serve event from the WireMock server's request journal
     * @param string $id
     */
    public function removeServeEvent($id): void
    {
        $this->doDelete("__admin/requests/$id");
    }

    /**
     * @param RequestPatternBuilder $requestPatternBuilder
     */
    public function removeServeEvents($requestPatternBuilder): void
    {
        $this->doPost('__admin/requests/remove', $requestPatternBuilder->build());
    }

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     */
    public function removeEventsByStubMetadata($valueMatchingStrategy): void
    {
        $this->doPost('__admin/requests/remove-by-metadata', $valueMatchingStrategy);
    }

    /**
     * Sets a delay on all stubbed responses
     *
     * @param int $delayMillis
     */
    public function setGlobalFixedDelay($delayMillis): void
    {
        $this->doPost('__admin/settings', GlobalDelaySettings::fixed($delayMillis));
    }

    /**
     * @param DelayDistribution $delayDistribution
     */
    public function setGlobalRandomDelay($delayDistribution): void
    {
        $this->doPost('__admin/settings', GlobalDelaySettings::random($delayDistribution));
    }

    /**
     * Note: this function isn't part of the Java API
     */
    public function resetGlobalDelays(): void
    {
        $this->doPost('__admin/settings', GlobalDelaySettings::none());
    }

    public function saveAllMappings(): void
    {
        $this->doPost('__admin/mappings/save');
    }

    /**
     * Deletes a particular stub, identified by it's GUID, from the server
     *
     * @param string $id A string representation of a GUID
     */
    public function removeStub($id): void
    {
        $this->doDelete('__admin/mappings/' . urlencode($id));
    }

    /**
     * Reset all stubbings and the request journal
     */
    public function reset(): void
    {
        $this->doPost('__admin/reset');
    }

    /**
     * Reset all stubbings, reload those from the mappings directory, and reset the request journal
     */
    public function resetToDefault(): void
    {
        $this->doPost('__admin/mappings/reset');
    }

    /**
     * @return GetScenariosResult
     */
    public function getAllScenarios()
    {
        return $this->doGet('__admin/scenarios', GetScenariosResult::class);
    }

    /**
     * Reset all scenarios to the Scenario.STARTED state
     */
    public function resetAllScenarios(): void
    {
        $this->doPost('__admin/scenarios/reset');
    }

    /**
     * Reset a single scenario to the Scenario.STARTED state
     * @param string $scenarioName
     */
    public function resetScenario($scenarioName): void
    {
        $this->doPut('__admin/scenarios/' . urlencode($scenarioName) . '/state');
    }

    /**
     * @param string $scenarioName
     * @param string $stateName
     */
    public function setScenarioState($scenarioName, $stateName): void
    {
        $this->doPut(
            '__admin/scenarios/' . urlencode($scenarioName) . '/state',
            array('state' => $stateName)
        );
    }

    public function shutdownServer(): void
    {
        $this->doPost('__admin/shutdown');
    }

    /**
     * @param ?int $limit
     * @param ?int $offset
     * @return ListStubMappingsResult
     */
    public function listAllStubMappings(?int $limit = null, ?int $offset = null)
    {
        $pathAndParams = '__admin/mappings';
        if ($limit !== null || $offset !== null) {
            $pathAndParams .= '?';
            if ($limit !== null) {
                $pathAndParams .= 'limit=' . $limit;
            }
            if ($limit !== null && $offset !== null) {
                $pathAndParams .= '&';
            }
            if ($offset !== null) {
                $pathAndParams .= 'offset=' . $offset;
            }
        }
        return $this->doGet($pathAndParams, ListStubMappingsResult::class);
    }

    /**
     * @param string $id GUID of stub to retrieve
     * @return StubMapping
     * @throws \Exception
     */
    public function getSingleStubMapping($id)
    {
        return $this->doGet('__admin/mappings/' . urlencode($id), StubMapping::class);
    }

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return ListStubMappingsResult
     */
    public function findStubsByMetadata($valueMatchingStrategy)
    {
        return $this->doPost('__admin/mappings/find-by-metadata', $valueMatchingStrategy, ListStubMappingsResult::class);
    }

    /**
     * @param ValueMatchingStrategy $valueMatchingStrategy
     */
    public function removeStubsByMetadata($valueMatchingStrategy): void
    {
        $this->doPost('__admin/mappings/remove-by-metadata', $valueMatchingStrategy);
    }

    /**
     * @param RecordSpecBuilder|string $recordingSpecOrUrl
     */
    public function startRecording($recordingSpecOrUrl): void
    {
        if (is_string($recordingSpecOrUrl)) {
            $spec = self::recordSpec()->forTarget($recordingSpecOrUrl)->build();
        } else {
            $spec = $recordingSpecOrUrl->build();
        }

        $this->doPost('__admin/recordings/start', $spec);
    }

    /**
     * @return RecordingStatusResult
     */
    public function getRecordingStatus()
    {
        return $this->doGet('__admin/recordings/status', RecordingStatusResult::class);
    }

    /**
     * @return SnapshotRecordResult
     */
    public function stopRecording()
    {
        return $this->doPost('__admin/recordings/stop', null, SnapshotRecordResult::class);
    }

    /**
     * @param RecordSpecBuilder $recordingSpecBuilder
     * @return SnapshotRecordResult
     */
    public function snapshotRecord($recordingSpecBuilder = null)
    {
        $recordingSpec = $recordingSpecBuilder ? $recordingSpecBuilder->build() : null;
        return $this->doPost('__admin/recordings/snapshot', $recordingSpec, SnapshotRecordResult::class);
    }

    /**
     * @template T of object
     * @param string $path
     * @param string|null|class-string<T> $resultType
     * @return mixed
     * @phpstan-return (
     *      $resultType is class-string<T>
     *          ? T
     *          : ($resultType is class-string ? object : mixed)
     *  )
     */
    private function doGet(string $path, ?string $resultType = null)
    {
        $url = $this->_makeUrl($path);
        $resultJson = $this->curl->get($url);
        if ($resultType != null) {
            return $this->serializer->deserialize($resultJson, $resultType);
        } else {
            return null;
        }
    }

    /**
     * @template T of object
     * @param string $path
     * @param mixed $body
     * @param string|null|class-string<T> $resultType
     * @return mixed
     * @phpstan-return (
     *       $resultType is class-string<T>
     *           ? T
     *           : ($resultType is class-string ? object : mixed)
     *   )
     */
    private function doPost(string $path, $body = null, ?string $resultType = null)
    {
        $url = $this->_makeUrl($path);
        if ($body != null) {
            $requestJson = $this->serializer->serialize($body);
        } else {
            $requestJson = null;
        }
        $resultJson = $this->curl->post($url, $requestJson);
        if ($resultType != null) {
            return $this->serializer->deserialize($resultJson, $resultType);
        } else {
            return null;
        }
    }

    /**
     * @template T of object
     * @param string $path
     * @param mixed $body
     * @param string|null|class-string<T> $resultType
     * @return mixed
     * @phpstan-return (
     *        $resultType is class-string<T>
     *            ? T
     *            : ($resultType is class-string ? object : mixed)
     *    )
     */
    private function doPut(string $path, $body = null, ?string $resultType = null)
    {
        $url = $this->_makeUrl($path);
        if ($body != null) {
            $requestJson = $this->serializer->serialize($body);
        } else {
            $requestJson = null;
        }
        $resultJson = $this->curl->put($url, $requestJson);
        if ($resultType != null) {
            return $this->serializer->deserialize($resultJson, $resultType);
        } else {
            return null;
        }
    }

    /**
     * @template T of object
     * @param string $path
     * @param string|null|class-string<T> $resultType
     * @return mixed
     * @phpstan-return (
     *        $resultType is class-string<T>
     *            ? T
     *            : ($resultType is class-string ? object : mixed)
     *    )
     */
    private function doDelete(string $path, ?string $resultType = null)
    {
        $url = $this->_makeUrl($path);
        $resultJson = $this->curl->delete($url);
        if ($resultType != null) {
            return $this->serializer->deserialize($resultJson, $resultType);
        } else {
            return null;
        }
    }

    private function _makeUrl(string $path): string
    {
        return "http://$this->hostname:$this->port/$path";
    }

    /**
     * @param UrlMatchingStrategy|string $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function get($urlMatchingStrategy): MappingBuilder
    {
        if (is_string($urlMatchingStrategy)) {
            $urlMatchingStrategy = self::urlEqualTo($urlMatchingStrategy);
        }
        $requestPattern = new RequestPatternBuilder('GET', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy|string $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function post($urlMatchingStrategy): MappingBuilder
    {
        if (is_string($urlMatchingStrategy)) {
            $urlMatchingStrategy = self::urlEqualTo($urlMatchingStrategy);
        }
        $requestPattern = new RequestPatternBuilder('POST', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy|string $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function put($urlMatchingStrategy): MappingBuilder
    {
        if (is_string($urlMatchingStrategy)) {
            $urlMatchingStrategy = self::urlEqualTo($urlMatchingStrategy);
        }
        $requestPattern = new RequestPatternBuilder('PUT', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy|string $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function delete($urlMatchingStrategy): MappingBuilder
    {
        if (is_string($urlMatchingStrategy)) {
            $urlMatchingStrategy = self::urlEqualTo($urlMatchingStrategy);
        }
        $requestPattern = new RequestPatternBuilder('DELETE', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy|string $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function patch($urlMatchingStrategy): MappingBuilder
    {
        if (is_string($urlMatchingStrategy)) {
            $urlMatchingStrategy = self::urlEqualTo($urlMatchingStrategy);
        }
        $requestPattern = new RequestPatternBuilder('PATCH', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy|string $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function head($urlMatchingStrategy): MappingBuilder
    {
        if (is_string($urlMatchingStrategy)) {
            $urlMatchingStrategy = self::urlEqualTo($urlMatchingStrategy);
        }
        $requestPattern = new RequestPatternBuilder('HEAD', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy|string $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function options($urlMatchingStrategy): MappingBuilder
    {
        if (is_string($urlMatchingStrategy)) {
            $urlMatchingStrategy = self::urlEqualTo($urlMatchingStrategy);
        }
        $requestPattern = new RequestPatternBuilder('OPTIONS', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy|string $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function trace($urlMatchingStrategy): MappingBuilder
    {
        if (is_string($urlMatchingStrategy)) {
            $urlMatchingStrategy = self::urlEqualTo($urlMatchingStrategy);
        }
        $requestPattern = new RequestPatternBuilder('TRACE', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy|string $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function any($urlMatchingStrategy): MappingBuilder
    {
        if (is_string($urlMatchingStrategy)) {
            $urlMatchingStrategy = self::urlEqualTo($urlMatchingStrategy);
        }
        $requestPattern = new RequestPatternBuilder('ANY', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param string $matcherName
     * @param array<int|string, mixed> $params
     * @return MappingBuilder
     */
    public static function requestMatching($matcherName, $params = array()): MappingBuilder
    {
        $requestPattern = new RequestPatternBuilder($matcherName, $params);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param string $url
     * @return UrlMatchingStrategy
     */
    public static function urlEqualTo($url): UrlMatchingStrategy
    {
        return new UrlMatchingStrategy('url', $url);
    }

    /**
     * @param string $urlRegex
     * @return UrlMatchingStrategy
     */
    public static function urlMatching($urlRegex): UrlMatchingStrategy
    {
        return new UrlMatchingStrategy('urlPattern', $urlRegex);
    }

    /**
     * @param string $urlPath
     * @return UrlMatchingStrategy
     */
    public static function urlPathEqualTo($urlPath): UrlMatchingStrategy
    {
        return new UrlMatchingStrategy('urlPath', $urlPath);
    }

    /**
     * @param string $urlPathRegex
     * @return UrlMatchingStrategy
     */
    public static function urlPathMatching($urlPathRegex): UrlMatchingStrategy
    {
        return new UrlMatchingStrategy('urlPathPattern', $urlPathRegex);
    }

    /**
     * @return UrlMatchingStrategy
     */
    public static function anyUrl(): UrlMatchingStrategy
    {
        return new UrlMatchingStrategy('urlPattern', '.*');
    }

    /**
     * @param string $value
     * @return EqualToMatchingStrategy
     */
    public static function equalTo($value): EqualToMatchingStrategy
    {
        return new EqualToMatchingStrategy($value);
    }

    /**
     * @param string $value
     * @return EqualToMatchingStrategy
     */
    public static function equalToIgnoreCase($value): EqualToMatchingStrategy
    {
        return new EqualToMatchingStrategy($value, true);
    }

    /**
     * @param string $base64String
     * @return ValueMatchingStrategy
     */
    public static function binaryEqualTo($base64String): ValueMatchingStrategy
    {
        return new ValueMatchingStrategy('binaryEqualTo', $base64String);
    }

    /**
     * @param string $value
     * @return ValueMatchingStrategy
     */
    public static function matching($value): ValueMatchingStrategy
    {
        return new ValueMatchingStrategy('matches', $value);
    }

    /**
     * @param string $value
     * @return ValueMatchingStrategy
     */
    public static function notMatching($value): ValueMatchingStrategy
    {
        return new ValueMatchingStrategy('doesNotMatch', $value);
    }

    /**
     * @param string $value
     * @return ValueMatchingStrategy
     */
    public static function containing($value): ValueMatchingStrategy
    {
        return new ValueMatchingStrategy('contains', $value);
    }

    /**
     * @param string $value
     * @return ValueMatchingStrategy
     */
    public static function notContaining($value): ValueMatchingStrategy
    {
        return new ValueMatchingStrategy('doesNotContain', $value);
    }

    /**
     * @param string $value
     * @param boolean|null $ignoreArrayOrder
     * @param boolean|null $ignoreExtraElements
     */
    public static function equalToJson($value, $ignoreArrayOrder = null, $ignoreExtraElements = null): JsonValueMatchingStrategy
    {
        return new JsonValueMatchingStrategy($value, $ignoreArrayOrder, $ignoreExtraElements);
    }

    /**
     * @param string $jsonPath
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return JsonPathValueMatchingStrategy
     */
    public static function matchingJsonPath($jsonPath, $valueMatchingStrategy = null): JsonPathValueMatchingStrategy
    {
        return new JsonPathValueMatchingStrategy(
            $valueMatchingStrategy === null ?
                $jsonPath :
                new AdvancedPathPattern($valueMatchingStrategy, $jsonPath));
    }

    /**
     * @param string $xml
     * @param bool $enablePlaceholders
     * @param string $placeholderOpeningDelimiterRegex
     * @param string $placeholderClosingDelimiterRegex
     * @return EqualToXmlMatchingStrategy
     */
    public static function equalToXml($xml,
        $enablePlaceholders = false,
        $placeholderOpeningDelimiterRegex = null,
        $placeholderClosingDelimiterRegex = null
    ): EqualToXmlMatchingStrategy {
        return new EqualToXmlMatchingStrategy(
            $xml,
            $enablePlaceholders,
            $placeholderOpeningDelimiterRegex,
            $placeholderClosingDelimiterRegex
        );
    }

    /**
     * @param string $xPath
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return XPathValueMatchingStrategy
     */
    public static function matchingXPath($xPath, $valueMatchingStrategy = null): XPathValueMatchingStrategy
    {
        return new XPathValueMatchingStrategy($xPath);
    }

    /**
     * @return MultipartValuePatternBuilder
     */
    public static function aMultipart(): MultipartValuePatternBuilder
    {
        return new MultipartValuePatternBuilder();
    }

    /**
     * @return ValueMatchingStrategy
     */
    public static function absent(): ValueMatchingStrategy
    {
        return new ValueMatchingStrategy('absent', true);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function lessThan($count): CountMatchingStrategy
    {
        return CountMatchingStrategy::lessThan($count);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function lessThanOrExactly($count): CountMatchingStrategy
    {
        return CountMatchingStrategy::lessThanOrExactly($count);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function exactly($count): CountMatchingStrategy
    {
        return CountMatchingStrategy::exactly($count);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function moreThanOrExactly($count): CountMatchingStrategy
    {
        return CountMatchingStrategy::moreThanOrExactly($count);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function moreThan($count): CountMatchingStrategy
    {
        return CountMatchingStrategy::moreThan($count);
    }

    /**
     * @param string $dateTimeSpec
     * @return DateTimeMatchingStrategy
     */
    public static function before($dateTimeSpec): DateTimeMatchingStrategy
    {
        return DateTimeMatchingStrategy::before($dateTimeSpec);
    }

    /**
     * @return DateTimeMatchingStrategy
     */
    public static function beforeNow(): DateTimeMatchingStrategy
    {
        return DateTimeMatchingStrategy::before("now");
    }

    /**
     * @param string $dateTimeSpec
     * @return DateTimeMatchingStrategy
     */
    public static function equalToDateTime($dateTimeSpec): DateTimeMatchingStrategy
    {
        return DateTimeMatchingStrategy::equalToDateTime($dateTimeSpec);
    }

    /**
     * @return DateTimeMatchingStrategy
     */
    public static function isNow(): DateTimeMatchingStrategy
    {
        return DateTimeMatchingStrategy::equalToDateTime("now");
    }

    /**
     * @param string $dateTimeSpec
     * @return DateTimeMatchingStrategy
     */
    public static function after($dateTimeSpec): DateTimeMatchingStrategy
    {
        return DateTimeMatchingStrategy::after($dateTimeSpec);
    }

    /**
     * @return DateTimeMatchingStrategy
     */
    public static function afterNow(): DateTimeMatchingStrategy
    {
        return DateTimeMatchingStrategy::after("now");
    }

    /**
     * @param ValueMatchingStrategy ...$matchers
     * @return LogicalOperatorMatchingStrategy
     */
    public static function and(...$matchers): LogicalOperatorMatchingStrategy
    {
        return LogicalOperatorMatchingStrategy::andAll(...$matchers);
    }

    /**
     * @param ValueMatchingStrategy ...$matchers
     * @return LogicalOperatorMatchingStrategy
     */
    public static function or(...$matchers): LogicalOperatorMatchingStrategy
    {
        return LogicalOperatorMatchingStrategy::orAll(...$matchers);
    }

    public static function recordSpec(): RecordSpecBuilder
    {
        return new RecordSpecBuilder();
    }

    public static function aResponse(): ResponseDefinitionBuilder
    {
        return new ResponseDefinitionBuilder();
    }

    public static function getRequestedFor(UrlMatchingStrategy $urlMatchingStrategy): RequestPatternBuilder
    {
        return new RequestPatternBuilder('GET', $urlMatchingStrategy);
    }

    public static function postRequestedFor(UrlMatchingStrategy $urlMatchingStrategy): RequestPatternBuilder
    {
        return new RequestPatternBuilder('POST', $urlMatchingStrategy);
    }

    public static function putRequestedFor(UrlMatchingStrategy $urlMatchingStrategy): RequestPatternBuilder
    {
        return new RequestPatternBuilder('PUT', $urlMatchingStrategy);
    }

    public static function deleteRequestedFor(UrlMatchingStrategy $urlMatchingStrategy): RequestPatternBuilder
    {
        return new RequestPatternBuilder('DELETE', $urlMatchingStrategy);
    }

    public static function optionsRequestedFor(UrlMatchingStrategy $urlMatchingStrategy): RequestPatternBuilder
    {
        return new RequestPatternBuilder('OPTIONS', $urlMatchingStrategy);
    }

    public static function patchRequestedFor(UrlMatchingStrategy $urlMatchingStrategy): RequestPatternBuilder
    {
        return new RequestPatternBuilder('PATCH', $urlMatchingStrategy);
    }

    public static function headRequestedFor(UrlMatchingStrategy $urlMatchingStrategy): RequestPatternBuilder
    {
        return new RequestPatternBuilder('HEAD', $urlMatchingStrategy);
    }

    public static function traceRequestedFor(UrlMatchingStrategy $urlMatchingStrategy): RequestPatternBuilder
    {
        return new RequestPatternBuilder('TRACE', $urlMatchingStrategy);
    }

    /**
     * @param string $body
     * @return ResponseDefinitionBuilder
     */
    public static function ok($body = null)
    {
        if ($body) {
            return self::aResponse()->withStatus(200)->withBody($body);
        } else {
            return self::aResponse()->withStatus(200);
        }
    }

    /**
     * @param string $contentType
     * @param string $body
     * @return ResponseDefinitionBuilder
     */
    public static function okForContentType($contentType, $body)
    {
        return self::aResponse()
            ->withStatus(200)
            ->withHeader('Content-Type', $contentType)
            ->withBody($body);
    }

    /**
     * @param string $body
     * @return ResponseDefinitionBuilder
     */
    public static function okJson($body)
    {
        return self::okForContentType('application/json', $body);
    }

    /**
     * @param string $body
     * @return ResponseDefinitionBuilder
     */
    public static function okXml($body)
    {
        return self::okForContentType('application/xml', $body);
    }

    /**
     * @param string $body
     * @return ResponseDefinitionBuilder
     */
    public static function okTextXml($body)
    {
        return self::okForContentType('text/xml', $body);
    }

    /**
     * @param string $body
     * @param integer $status
     * @return ResponseDefinitionBuilder
     */
    public static function jsonResponse($body, $status)
    {
        return self::aResponse()
            ->withStatus($status)
            ->withHeader('Content-Type', "application/json")
            ->withBody($body);
    }

    /**
     * @param string $url
     * @return MappingBuilder
     */
    public static function proxyAllTo($url)
    {
        return self::any(self::anyUrl())->willReturn(self::aResponse()->proxiedFrom($url));
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function created() {
        return self::aResponse()->withStatus(201);
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function noContent()
    {
        return self::aResponse()->withStatus(204);
    }

    /**
     * @param string $location
     * @return ResponseDefinitionBuilder
     */
    public static function permanentRedirect($location)
    {
        return self::aResponse()->withStatus(301)->withHeader('Location', $location);
    }

    /**
     * @param string $location
     * @return ResponseDefinitionBuilder
     */
    public static function temporaryRedirect($location)
    {
        return self::aResponse()->withStatus(302)->withHeader('Location', $location);
    }

    /**
     * @param string $location
     * @return ResponseDefinitionBuilder
     */
    public static function seeOther($location)
    {
        return self::aResponse()->withStatus(303)->withHeader('Location', $location);
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function badRequest()
    {
        return self::aResponse()->withStatus(400);
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function badRequestEntity()
    {
        return self::aResponse()->withStatus(422);
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function unauthorized()
    {
        return self::aResponse()->withStatus(401);
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function forbidden()
    {
        return self::aResponse()->withStatus(403);
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function notFound()
    {
        return self::aResponse()->withStatus(404);
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function serverError()
    {
        return self::aResponse()->withStatus(500);
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function serviceUnavailable()
    {
        return self::aResponse()->withStatus(503);
    }

    /**
     * @param int $status
     * @return ResponseDefinitionBuilder
     */
    public static function status($status)
    {
        return self::aResponse()->withStatus($status);
    }

    public static function stubImport(): StubImportBuilder
    {
        return new StubImportBuilder();
    }

    public static function webhook(): WebhookDefinition
    {
        return new WebhookDefinition();
    }
}
