<?php

namespace WireMock\PostServe;

use WireMock\Client\ResponseDefinitionBuilder;
use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;
use WireMock\Fault\LogNormal;
use WireMock\Fault\UniformDistribution;
use WireMock\Serde\NormalizerUtils;
use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;

class WebhookDefinition implements PostNormalizationAmenderInterface, PreDenormalizationAmenderInterface
{
    /** @var string */
    private $method;
    /** @var string */
    private $url;
    /** @var string[] */
    private $headers = null;
    /** @var string */
    private $body;
    /** @var string */
    private $base64Body;
    /** @var DelayDistribution */
    private $delay;
    /** @var array */
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
        $this->delay = array(
            'type' => 'fixed',
            'milliseconds' => $delayMillis,
        );
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

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        NormalizerUtils::inline($normalisedArray, 'extraParameters');
        return $normalisedArray;
    }

    public static function amendPreNormalisation(array $normalisedArray): array
    {
        $extraParameters = array_diff_key($normalisedArray, ['method', 'url', 'headers', 'body', 'base64Body', 'delay']);
        foreach ($extraParameters as $key => $value) {
            unset($normalisedArray[$key]);
        }
        $normalisedArray['extraParameters'] = $extraParameters;
        return $normalisedArray;
    }
}