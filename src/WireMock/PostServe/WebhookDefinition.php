<?php

namespace WireMock\PostServe;

use WireMock\Client\ResponseDefinitionBuilder;
use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;
use WireMock\Fault\FixedDelay;
use WireMock\Fault\LogNormal;
use WireMock\Fault\UniformDistribution;
use WireMock\Serde\NormalizerUtils;
use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;

class WebhookDefinition implements PostNormalizationAmenderInterface, PreDenormalizationAmenderInterface
{
    /** @var string|null */
    private $method;
    /** @var string|null */
    private $url;
    /** @var string[]|null */
    private $headers = null;
    /** @var string|null */
    private $body;
    /** @var string|null */
    private $base64Body;
    /** @var DelayDistribution|null */
    private $delay;
    /** @var array|null */
    private $extraParameters = null; // TODO: Check this is accepted by WireMock

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
        if (!isset($this->headers)) {
            $this->headers = array();
        }
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
        if (!isset($this->extraParameters)) {
            $this->extraParameters = array();
        }
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

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        NormalizerUtils::inline($normalisedArray, 'extraParameters');
        return $normalisedArray;
    }

    public static function amendPreDenormalisation(array $normalisedArray): array
    {
        $standardPropNames = ['method', 'url', 'headers', 'body', 'base64Body', 'delay'];
        $standardProps = [];
        foreach ($standardPropNames as $propName) {
            if (array_key_exists($propName, $normalisedArray)) {
                $standardProps[$propName] = $normalisedArray[$propName];
                unset($normalisedArray[$propName]);
            }
        }
        if (!empty($normalisedArray)) {
            $standardProps['extraParameters'] = $normalisedArray;
        }
        return $standardProps;
    }
}