<?php

namespace WireMock\Client;

use WireMock\Matching\RequestPattern;
use WireMock\Matching\UrlMatchingStrategy;
use WireMock\Stubbing\StubMapping;

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

    public static function create($hostname='localhost', $port=8080)
    {
        $httpWait = new HttpWait();
        $curl = new Curl();
        return new self($httpWait, $curl, $hostname, $port);
    }

    function __construct(HttpWait $httpWait, Curl $curl, $hostname='localhost', $port=8080)
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

    public function stubFor(MappingBuilder $mappingBuilder)
    {
        $stubMapping = $mappingBuilder->build();
        $url = $this->_makeUrl('__admin/mappings/new');
        $this->_curl->post($url, $stubMapping->toArray());
        return $stubMapping;
    }

    /**
     * @param RequestPatternBuilder|integer $requestPatternBuilderOrNumber
     * @param RequestPatternBuilder $requestPatternBuilder
     * @throws VerificationException
     */
    public function verify($requestPatternBuilderOrNumber, RequestPatternBuilder $requestPatternBuilder=null)
    {
        if (is_int($requestPatternBuilderOrNumber)) {
            $patternBuilder = $requestPatternBuilder;
            $numberOfRequests = $requestPatternBuilderOrNumber;
        } else {
            $patternBuilder = $requestPatternBuilderOrNumber;
            $numberOfRequests = null;
        }

        $requestPattern = $patternBuilder->build();
        $url = $this->_makeUrl('__admin/requests/count');
        $responseJson = $this->_curl->post($url, $requestPattern->toArray());
        $response = json_decode($responseJson, true);
        $count = $response['count'];

        if (($numberOfRequests === null && $count < 1) || ($numberOfRequests && $count != $numberOfRequests)) {
            $expected = $numberOfRequests ? $numberOfRequests : 'at least one';
            throw new VerificationException("Expected $expected request(s), but found $count");
        }
    }

    public function findAll(RequestPatternBuilder $requestPatternBuilder)
    {
        $requestPattern = $requestPatternBuilder->build();
        $url = $this->_makeUrl('__admin/requests/find');
        $findResultJson = $this->_curl->post($url, $requestPattern->toArray());
        $findResultArray = json_decode($findResultJson, true);
        $requestArrays = $findResultArray['requests'];
        $requests = array();
        foreach($requestArrays as $responseArray) {
            $requests[] = new LoggedRequest($responseArray);
        }
        return $requests;
    }

    public function reset()
    {
        $url = $this->_makeUrl('__admin/reset');
        $this->_curl->post($url);
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

    public static function urlMatching($urlRegex)
    {
        return new UrlMatchingStrategy('urlPattern', $urlRegex);
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
     * @param string $jsonPath
     * @return ValueMatchingStrategy
     */
    public static function matchingJsonPath($jsonPath)
    {
        return new ValueMatchingStrategy('matchesJsonPath', $jsonPath);
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

    //TODO: setGlobalFixedDelay
    //TODO: addRequestProcessingDelay
}