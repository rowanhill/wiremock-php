<?php

namespace WireMock\Client;

use WireMock\Matching\RequestPattern;
use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Stubbing\StubMapping;

class NearMiss implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var LoggedRequest */
    private $_request;
    /** @var StubMapping */
    private $_stubMapping;
    /** @var RequestPattern */
    private $_requestPattern;
    /** @var MatchResult */
    private $_matchResult;

    /**
     * @param LoggedRequest $request
     * @param StubMapping|null $stubMapping
     * @param RequestPattern|null $requestPattern
     * @param MatchResult $matchResult
     */
    public function __construct(
        LoggedRequest $request,
        ?StubMapping $stubMapping,
        ?RequestPattern $requestPattern,
        MatchResult $matchResult
    ) {
        $this->_request = $request;
        $this->_stubMapping = $stubMapping;
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
        return $this->_stubMapping;
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
}