<?php

namespace WireMock\Client;

use DateTime;
use WireMock\Matching\RequestPattern;
use WireMock\Matching\UrlMatchingStrategy;
use WireMock\Stubbing\StubMapping;
use WireMock\Verification\CountMatchingStrategy;

class WireMock
{
    /** @var string */
    private $_hostname;
    /** @var int */
    private $_port;
    /** @var HttpWait */
    private $_httpWait;
    /** @var Curl  */
    private $_curl;

    public static function create($hostname = 'localhost', $port = 8080)
    {
        $httpWait = new HttpWait();
        $curl = new Curl();
        return new self($httpWait, $curl, $hostname, $port);
    }

    public function __construct(HttpWait $httpWait, Curl $curl, $hostname = 'localhost', $port = 8080)
    {
        $this->_hostname = $hostname;
        $this->_port = $port;
        $this->_httpWait = $httpWait;
        $this->_curl = $curl;
    }

    public function isAlive()
    {
        $url = $this->_makeUrl('__admin/');
        return $this->_httpWait->waitForServerToGive200($url);
    }

    public function isShutDown()
    {
        $url = $this->_makeUrl('__admin/');
        return $this->_httpWait->waitForServerToFailToRespond($url);
    }

    public function stubFor(MappingBuilder $mappingBuilder)
    {
        $stubMapping = $mappingBuilder->build();
        $url = $this->_makeUrl('__admin/mappings');
        $result = $this->_curl->post($url, $stubMapping->toArray());
        $resultJson = json_decode($result);
        $stubMapping->setId($resultJson->id);
        return $stubMapping;
    }

    public function editStub(MappingBuilder $mappingBuilder)
    {
        $stubMapping = $mappingBuilder->build();
        if (!$stubMapping->getId()) {
            throw new VerificationException('Cannot edit a stub without an id');
        }
        $url = $this->_makeUrl('__admin/mappings/' . urlencode($stubMapping->getId()));
        $this->_curl->put($url, $stubMapping->toArray());
        return $stubMapping;
    }

    /**
     * @param RequestPatternBuilder|CountMatchingStrategy|integer $requestPatternBuilderOrCount
     * @param RequestPatternBuilder $requestPatternBuilder
     * @throws VerificationException
     */
    public function verify($requestPatternBuilderOrCount, RequestPatternBuilder $requestPatternBuilder = null)
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

        $requestPattern = $patternBuilder->build();
        $url = $this->_makeUrl('__admin/requests/count');
        $responseJson = $this->_curl->post($url, $requestPattern->toArray());
        $response = json_decode($responseJson, true);
        $count = $response['count'];

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
     * @param DateTime $since
     * @param int $limit
     * @return GetServeEventsResult
     */
    public function getAllServeEvents($since = null, $limit = null)
    {
        $pathAndParams = '__admin/requests';
        if ($since || $limit) {
            $pathAndParams .= '?';
            if ($since) {
                $pathAndParams .= 'since=' . urlencode($since->format(DateTime::ATOM));
            }
            if ($since && $limit) {
                $pathAndParams .= '&';
            }
            if ($limit) {
                $pathAndParams .= 'limit=' . urlencode($limit);
            }
        }
        $url = $this->_makeUrl($pathAndParams);
        $result = file_get_contents($url);
        $resultArray = json_decode($result, true);
        return new GetServeEventsResult($resultArray);
    }

    /**
     * @param RequestPatternBuilder $requestPatternBuilder
     * @return LoggedRequest[]
     */
    public function findAll(RequestPatternBuilder $requestPatternBuilder)
    {
        $requestPattern = $requestPatternBuilder->build();
        $url = $this->_makeUrl('__admin/requests/find');
        $findResultJson = $this->_curl->post($url, $requestPattern->toArray());
        $findResultArray = json_decode($findResultJson, true);
        $requestArrays = $findResultArray['requests'];
        $requests = array();
        foreach ($requestArrays as $responseArray) {
            $requests[] = new LoggedRequest($responseArray);
        }
        return $requests;
    }

    /**
     * @return UnmatchedRequests
     */
    public function findUnmatchedRequests()
    {
        $url = $this->_makeUrl('__admin/requests/unmatched');
        $resultJson = file_get_contents($url);
        $resultArray = json_decode($resultJson, true);
        return new UnmatchedRequests($resultArray);
    }

    /**
     * @param LoggedRequest|RequestPattern $loggedRequestOrPattern
     * @return FindNearMissesResult
     * @throws \Exception
     */
    public function findNearMissesFor($loggedRequestOrPattern)
    {
        if ($loggedRequestOrPattern instanceof LoggedRequest) {
            $url = $this->_makeUrl('__admin/near-misses/request');
        } else if ($loggedRequestOrPattern instanceof RequestPatternBuilder) {
            $loggedRequestOrPattern = $loggedRequestOrPattern->build();
            $url = $this->_makeUrl('__admin/near-misses/request-pattern');
        } else {
            throw new \Exception('Unexpected near miss specifier: ' . print_r($loggedRequestOrPattern, true));
        }
        $loggedRequestArray = $loggedRequestOrPattern->toArray();
        $findResultJson = $this->_curl->post($url, $loggedRequestArray);
        $findResult = json_decode($findResultJson, true);
        return FindNearMissesResult::fromArray($findResult);
    }

    /**
     * @return FindNearMissesResult
     */
    public function findNearMissesForAllUnmatched()
    {
        $url = $this->_makeUrl('__admin/requests/unmatched/near-misses');
        $findResultJson = file_get_contents($url);
        $findResult = json_decode($findResultJson, true);
        return FindNearMissesResult::fromArray($findResult);
    }

    /**
     * Deletes all serve events from the WireMock server's request journal
     */
    public function resetAllRequests()
    {
        $url = $this->_makeUrl('__admin/requests');
        $this->_curl->delete($url);
    }

    /**
     * Sets a delay on all stubbed responses
     *
     * @param int $delayMillis
     */
    public function setGlobalFixedDelay($delayMillis)
    {
        $url = $this->_makeUrl('__admin/settings');
        $this->_curl->post($url, array('fixedDelay' => $delayMillis));
    }

    public function saveAllMappings()
    {
        $url = $this->_makeUrl('__admin/mappings/save');
        $this->_curl->post($url);
    }

    /**
     * Deletes a particular stub, identified by it's GUID, from the server
     *
     * @param string $id A string representation of a GUID
     */
    public function removeStub($id)
    {
        $url = $this->_makeUrl('__admin/mappings/' . urlencode($id));
        $this->_curl->delete($url);
    }

    /**
     * Reset all stubbings and the request journal
     */
    public function reset()
    {
        $url = $this->_makeUrl('__admin/reset');
        $this->_curl->post($url);
    }

    /**
     * Reset all stubbings, reload those from the mappings directory, and reset the request journal
     */
    public function resetToDefault()
    {
        $url = $this->_makeUrl('__admin/mappings/reset');
        $this->_curl->post($url);
    }

    /**
     * Reset all scenarios to the Scenario.STARTED state
     */
    public function resetAllScenarios()
    {
        $url = $this->_makeUrl('__admin/scenarios/reset');
        $this->_curl->post($url);
    }

    public function shutdownServer()
    {
        $url = $this->_makeUrl('__admin/shutdown');
        $this->_curl->post($url);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return ListStubMappingsResult
     */
    public function listAllStubMappings($limit = null, $offset = null)
    {
        $pathAndParams = '__admin/mappings';
        if ($limit || $offset) {
            $pathAndParams .= '?';
            if ($limit) {
                $pathAndParams .= 'limit=' . urlencode($limit);
            }
            if ($limit && $offset) {
                $pathAndParams .= '&';
            }
            if ($offset) {
                $pathAndParams .= 'offset=' . urlencode($offset);
            }
        }
        $url = $this->_makeUrl($pathAndParams);
        $result = file_get_contents($url);
        $resultArray = json_decode($result, true);
        return new ListStubMappingsResult($resultArray);
    }

    /**
     * @param string $id GUID of stub to retrieve
     * @return StubMapping
     * @throws \Exception
     */
    public function getSingleStubMapping($id)
    {
        $url = $this->_makeUrl('__admin/mappings/' . urlencode($id));
        $result = file_get_contents($url);
        $resultArray = json_decode($result, true);
        return StubMapping::fromArray($resultArray);
    }

    private function _makeUrl($path)
    {
        return "http://$this->_hostname:$this->_port/$path";
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function get(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPatternBuilder('GET', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function post(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPatternBuilder('POST', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function put(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPatternBuilder('PUT', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function delete(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPatternBuilder('DELETE', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function patch(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPatternBuilder('PATCH', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function head(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPatternBuilder('HEAD', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function options(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPatternBuilder('OPTIONS', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function trace(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPatternBuilder('TRACE', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function any(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPatternBuilder('ANY', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param string $url
     * @return UrlMatchingStrategy
     */
    public static function urlEqualTo($url)
    {
        return new UrlMatchingStrategy('url', $url);
    }

    /**
     * @param string $urlRegex
     * @return UrlMatchingStrategy
     */
    public static function urlMatching($urlRegex)
    {
        return new UrlMatchingStrategy('urlPattern', $urlRegex);
    }

    /**
     * @param string $urlPath
     * @return UrlMatchingStrategy
     */
    public static function urlPathEqualTo($urlPath)
    {
        return new UrlMatchingStrategy('urlPath', $urlPath);
    }

    /**
     * @param string $urlPathRegex
     * @return UrlMatchingStrategy
     */
    public static function urlPathMatching($urlPathRegex)
    {
        return new UrlMatchingStrategy('urlPathPattern', $urlPathRegex);
    }

    /**
     * @return UrlMatchingStrategy
     */
    public static function anyUrl()
    {
        return new UrlMatchingStrategy('urlPattern', '.*');
    }

    /**
     * @param string $value
     * @return EqualToMatchingStrategy
     */
    public static function equalTo($value)
    {
        return new EqualToMatchingStrategy($value);
    }

    /**
     * @param string $value
     * @return EqualToMatchingStrategy
     */
    public static function equalToIgnoreCase($value)
    {
        return new EqualToMatchingStrategy($value, true);
    }

    /**
     * @param string $base64String
     * @return ValueMatchingStrategy
     */
    public static function binaryEqualTo($base64String)
    {
        return new ValueMatchingStrategy('binaryEqualTo', $base64String);
    }

    /**
     * @param string $value
     * @return ValueMatchingStrategy
     */
    public static function matching($value)
    {
        return new ValueMatchingStrategy('matches', $value);
    }

    /**
     * @param string $value
     * @return ValueMatchingStrategy
     */
    public static function notMatching($value)
    {
        return new ValueMatchingStrategy('doesNotMatch', $value);
    }

    /**
     * @param string $value
     * @return ValueMatchingStrategy
     */
    public static function containing($value)
    {
        return new ValueMatchingStrategy('contains', $value);
    }

    /**
     * @param string $value
     * @param boolean $ignoreArrayOrder
     * @param boolean $ignoreExtraElements
     * @return ValueMatchingStrategy
     */
    public static function equalToJson($value, $ignoreArrayOrder = null, $ignoreExtraElements = null)
    {
        return new JsonValueMatchingStrategy($value, $ignoreArrayOrder, $ignoreExtraElements);
    }

    /**
     * @param string $jsonPath
     * @return ValueMatchingStrategy
     */
    public static function matchingJsonPath($jsonPath)
    {
        return new ValueMatchingStrategy('matchesJsonPath', $jsonPath);
    }

    /**
     * @param string $xml
     * @return ValueMatchingStrategy
     */
    public static function equalToXml($xml)
    {
        return new ValueMatchingStrategy('equalToXml', $xml);
    }

    /**
     * @param string $xPath
     * @param ValueMatchingStrategy $valueMatchingStrategy
     * @return XPathValueMatchingStrategy
     */
    public static function matchingXPath($xPath, $valueMatchingStrategy = null)
    {
        return new XPathValueMatchingStrategy($xPath, $valueMatchingStrategy);
    }

    /**
     * @return MultipartValuePatternBuilder
     */
    public static function aMultipart()
    {
        return new MultipartValuePatternBuilder();
    }

    /**
     * @return ValueMatchingStrategy
     */
    public static function absent()
    {
        return new ValueMatchingStrategy('absent', true);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function lessThan($count)
    {
        return CountMatchingStrategy::lessThan($count);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function lessThanOrExactly($count)
    {
        return CountMatchingStrategy::lessThanOrExactly($count);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function exactly($count)
    {
        return CountMatchingStrategy::exactly($count);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function moreThanOrExactly($count)
    {
        return CountMatchingStrategy::moreThanOrExactly($count);
    }

    /**
     * @param int $count
     * @return CountMatchingStrategy
     */
    public static function moreThan($count)
    {
        return CountMatchingStrategy::moreThan($count);
    }

    /**
     * @return ResponseDefinitionBuilder
     */
    public static function aResponse()
    {
        return new ResponseDefinitionBuilder();
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return RequestPatternBuilder
     */
    public static function getRequestedFor(UrlMatchingStrategy $urlMatchingStrategy)
    {
        return new RequestPatternBuilder('GET', $urlMatchingStrategy);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return RequestPatternBuilder
     */
    public static function postRequestedFor(UrlMatchingStrategy $urlMatchingStrategy)
    {
        return new RequestPatternBuilder('POST', $urlMatchingStrategy);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return RequestPatternBuilder
     */
    public static function putRequestedFor(UrlMatchingStrategy $urlMatchingStrategy)
    {
        return new RequestPatternBuilder('PUT', $urlMatchingStrategy);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return RequestPatternBuilder
     */
    public static function deleteRequestedFor(UrlMatchingStrategy $urlMatchingStrategy)
    {
        return new RequestPatternBuilder('DELETE', $urlMatchingStrategy);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return RequestPatternBuilder
     */
    public static function optionsRequestedFor(UrlMatchingStrategy $urlMatchingStrategy)
    {
        return new RequestPatternBuilder('OPTIONS', $urlMatchingStrategy);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return RequestPatternBuilder
     */
    public static function patchRequestedFor(UrlMatchingStrategy $urlMatchingStrategy)
    {
        return new RequestPatternBuilder('PATCH', $urlMatchingStrategy);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return RequestPatternBuilder
     */
    public static function headRequestedFor(UrlMatchingStrategy $urlMatchingStrategy)
    {
        return new RequestPatternBuilder('HEAD', $urlMatchingStrategy);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return RequestPatternBuilder
     */
    public static function traceRequestedFor(UrlMatchingStrategy $urlMatchingStrategy)
    {
        return new RequestPatternBuilder('TRACE', $urlMatchingStrategy);
    }
}
