<?php

namespace WireMock\PostServe;

use WireMock\Fault\DelayDistribution;
use WireMock\Fault\FixedDelay;
use WireMock\Fault\LogNormal;
use WireMock\Fault\UniformDistribution;

class WebhookDefinition
{
    /** @var string|null */
    private $method;
    /** @var string|null */
    private $url;
    /** @var string[]|null */
    private $headers;
    /** @var string|null */
    private $body;
    /** @var string|null */
    private $base64Body;
    /** @var DelayDistribution|null */
    private $delay;
    /**
     * @var array|null
     * @serde-catch-all
     */
    private $extraParameters;

    public function withMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function withUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function withHeader(string $header, string $value): self
    {
        $this->headers[$header] = $value;
        return $this;
    }

    public function withBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function withBodyData(string $bytesAsString): self
    {
        $base64 = base64_encode($bytesAsString);
        $this->base64Body = $base64;
        return $this;
    }

    public function withFixedDelay(int $delayMillis): self
    {
        $this->delay = new FixedDelay($delayMillis);
        return $this;
    }

    public function withRandomDelay(DelayDistribution $delayDistribution): self
    {
        $this->delay = $delayDistribution;
        return $this;
    }

    public function withLogNormalRandomDelay(float $median, float $sigma): self
    {
        return $this->withRandomDelay(new LogNormal($median, $sigma));
    }

    public function withUniformRandomDelay(int $lower, int $upper): self
    {
        return $this->withRandomDelay(new UniformDistribution($lower, $upper));
    }

    public function withExtraParameter(string $name, $value): self
    {
        $this->extraParameters[$name] = $value;
        return $this;
    }

    // TODO: Split out getters and fluent builder methods into separate classes

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return string[]|null
     */
    public function getHeaders(): ?array
    {
        return $this->headers;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @return string|null
     */
    public function getBase64Body(): ?string
    {
        return $this->base64Body;
    }

    /**
     * @return DelayDistribution|null
     */
    public function getDelay(): ?DelayDistribution
    {
        return $this->delay;
    }

    /**
     * @return array|null
     */
    public function getExtraParameters(): ?array
    {
        return $this->extraParameters;
    }
}