<?php

namespace WireMock\Client;

use DateTime;
use WireMock\Matching\RequestPattern;
use WireMock\Matching\UrlMatchingStrategy;
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
        $stubMapping->id = $resultJson->id;
        return $stubMapping;
    }

    public function editStub(MappingBuilder $mappingBuilder)
    {
        $stubMapping = $mappingBuilder->build();
        if (!$stubMapping->id) {
            throw new VerificationException('Cannot edit a stub without an id');
        }
        $url = $this->_makeUrl('__admin/mappings/' . urlencode($stubMapping->id));
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
     * @return array Associative array from JSON - see WireMock docs for details
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
        $resultObj = json_decode($result, true);
        return $resultObj;
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
     * @return array Associative array from JSON - see WireMock docs for details
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
        $resultObj = json_decode($result, true);
        return $resultObj;
    }

    /**
     * @param string $id GUID of stub to retrieve
     * @return \stdClass
     */
    public function getSingleStubMapping($id)
    {
        $url = $this->_makeUrl('__admin/mappings/' . urlencode($id));
        $result = file_get_contents($url);
        $resultObj = json_decode($result, true);
        return $resultObj;
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
        $requestPattern = new RequestPattern('GET', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function post(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPattern('POST', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function put(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPattern('PUT', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function delete(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPattern('DELETE', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function patch(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPattern('PATCH', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function head(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPattern('HEAD', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function options(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPattern('OPTIONS', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function trace(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPattern('TRACE', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param UrlMatchingStrategy $urlMatchingStrategy
     * @return MappingBuilder
     */
    public static function any(UrlMatchingStrategy $urlMatchingStrategy)
    {
        $requestPattern = new RequestPattern('ANY', $urlMatchingStrategy);
        return new MappingBuilder($requestPattern);
    }

    /**
     * @param string $urlPath
     * @return UrlMatchingStrategy
     */
    public static function urlEqualTo($urlPath)
    {
        return new UrlMatchingStrategy('url', $urlPath);
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
     * @return UrlMatchingStrategy
     */
    public static function anyUrl()
    {
        return new UrlMatchingStrategy('urlPattern', '.*');
    }

    /**
     * @param string $value
     * @return ValueMatchingStrategy
     */
    public static function equalTo($value)
    {
        return new ValueMatchingStrategy('equalTo', $value);
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
     * @param string $jsonCompareMode
     * @return ValueMatchingStrategy
     */
    public static function equalToJson($value, $jsonCompareMode = JsonValueMatchingStrategy::COMPARE_MODE__NON_EXTENSIBLE)
    {
        return new JsonValueMatchingStrategy($value, $jsonCompareMode);
    }

    /**
     * @param string $jsonPath
     * @return ValueMatchingStrategy
     */
    public static function matchingJsonPath($jsonPath)
    {
        return new ValueMatchingStrategy('matchesJsonPath', $jsonPath);
    }

    public static function matchingXPath($xPath)
    {
        return new ValueMatchingStrategy('matchesXPath', $xPath);
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
