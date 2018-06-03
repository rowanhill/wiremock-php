<?php

namespace WireMock\Client;

use WireMock\Matching\RequestPattern;
use WireMock\Stubbing\StubMapping;

class NearMiss
{
    /** @var LoggedRequest */
    private $_request;
    /** @var StubMapping */
    private $_mapping;
    /** @var RequestPattern */
    private $_requestPattern;
    /** @var MatchResult */
    private $_matchResult;

    /**
     * @param LoggedRequest $request
     * @param StubMapping|null $mapping
     * @param RequestPattern|null $requestPattern
     * @param MatchResult $matchResult
     */
    public function __construct(
        LoggedRequest $request,
        $mapping,
        $requestPattern,
        MatchResult $matchResult
    ) {
        $this->_request = $request;
        $this->_mapping = $mapping;
        $this->_requestPattern = $requestPattern;
        $this->_matchResult = $matchResult;
    }

    /**
     * @return LoggedRequest
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return StubMapping
     */
    public function getMapping()
    {
        return $this->_mapping;
    }

    /**
     * @return RequestPattern
     */
    public function getRequestPattern()
    {
        return $this->_requestPattern;
    }

    /**
     * @return MatchResult
     */
    public function getMatchResult()
    {
        return $this->_matchResult;
    }

    /**
     * @param array $array
     * @return NearMiss
     * @throws \Exception
     */
    public static function fromArray(array $array)
    {
        return new NearMiss(
            LoggedRequest::fromArray($array['request']),
            $array['stubMapping'] ? StubMapping::fromArray($array['stubMapping']) : null,
            $array['requestPattern'] ? RequestPattern::fromArray($array['requestPattern']) : null,
            MatchResult::fromArray($array['matchResult'])
        );
    }
}