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
    private $_method;
    /** @var string */
    private $_url;
    /** @var string[] */
    private $_headers = null;
    /** @var string */
    private $_body;
    /** @var string */
    private $_base64Body;
    /** @var array */
    private $_delay;
    /** @var array */
    private $_extraParameters = null; // TODO: Check this is accepted by WireMock

    public function withMethod(string $method): self
    {
        $this->_method = $method;
        return $this;
    }

    public function withUrl(string $url): self
    {
        $this->_url = $url;
        return $this;
    }

    public function withHeader(string $header, string $value): self
    {
        if (!isset($this->_headers)) {
            $this->_headers = array();
        }
        $this->_headers[$header] = $value;
        return $this;
    }

    public function withBody(string $body): self
    {
        $this->_body = $body;
        return $this;
    }

    public function withBodyData(string $bytesAsString): self
    {
        $base64 = base64_encode($bytesAsString);
        $this->_base64Body = $base64;
        return $this;
    }

    public function withFixedDelay(int $delayMillis): self
    {
        $this->_delay = array(
            'type' => 'fixed',
            'milliseconds' => $delayMillis,
        );
        return $this;
    }

    public function withRandomDelay(DelayDistribution $delayDistribution): self
    {
        $this->_delay = $delayDistribution;
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
        if (!isset($this->_extraParameters)) {
            $this->_extraParameters = array();
        }
        $this->_extraParameters[$name] = $value;
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