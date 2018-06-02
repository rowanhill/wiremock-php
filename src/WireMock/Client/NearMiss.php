<?php

namespace WireMock\Client;

use WireMock\Stubbing\StubMapping;

class NearMiss
{
    /** @var LoggedRequest */
    private $_request;
    /** @var StubMapping */
    private $_mapping;
    /** @var MatchResult */
    private $_matchResult;

    /**
     * @param LoggedRequest $request
     * @param StubMapping $mapping
     * @param MatchResult $matchResult
     */
    public function __construct(
        LoggedRequest $request,
        StubMapping $mapping,
        MatchResult $matchResult
    ) {
        $this->_request = $request;
        $this->_mapping = $mapping;
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
            StubMapping::fromArray($array['stubMapping']),
            MatchResult::fromArray($array['matchResult'])
        );
    }
}