<?php

namespace WireMock\Recording;

use WireMock\Matching\RequestPattern;

class ProxiedServeEventFilters
{
    /**
     * @var RequestPattern|null
     * @serde-unwrapped
     */
    private $requestPattern;

    /**
     * @var string[]|null
     * @serde-name ids
     */
    private $requestIds;

    /** @var ?boolean */
    private $allowNonProxied;

    public function __construct(?RequestPattern $requestPattern, ?array $requestIds, ?bool $allowNonProxied)
    {
        $this->requestPattern = $requestPattern;
        $this->requestIds = $requestIds;
        $this->allowNonProxied = $allowNonProxied;
    }

    public function getRequestPattern(): ?RequestPattern
    {
        return $this->requestPattern;
    }

    public function getRequestIds(): ?array
    {
        return $this->requestIds;
    }

    public function getAllowNonProxied(): ?bool
    {
        return $this->allowNonProxied;
    }
}