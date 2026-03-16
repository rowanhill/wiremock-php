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

    public function getRequest()
    {
        return $this->request;
    }

    public function getMapping()
    {
        return $this->stubMapping;
    }

    public function getRequestPattern(): ?RequestPattern
    {
        return $this->requestPattern;
    }

    public function getMatchResult(): MatchResult
    {
        return $this->matchResult;
    }
}
