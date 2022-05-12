<?php

namespace WireMock\Client;

use WireMock\Matching\RequestPattern;
use WireMock\Stubbing\StubMapping;

class NearMiss
{
    /** @var LoggedRequest */
    private $request;
    /** @var ?StubMapping */
    private $stubMapping;
    /** @var ?RequestPattern */
    private $requestPattern;
    /** @var MatchResult */
    private $matchResult;

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
        $this->request = $request;
        $this->stubMapping = $stubMapping;
        $this->requestPattern = $requestPattern;
        $this->matchResult = $matchResult;
    }

    /**
     * @return LoggedRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return StubMapping
     */
    public function getMapping()
    {
        return $this->stubMapping;
    }

    /**
     * @return RequestPattern
     */
    public function getRequestPattern()
    {
        return $this->requestPattern;
    }

    /**
     * @return MatchResult
     */
    public function getMatchResult()
    {
        return $this->matchResult;
    }
}