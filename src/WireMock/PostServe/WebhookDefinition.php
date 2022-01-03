<?php

namespace WireMock\PostServe;

use WireMock\Client\ResponseDefinitionBuilder;
use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;
use WireMock\Fault\LogNormal;
use WireMock\Fault\UniformDistribution;
use WireMock\Serde\NormalizerUtils;
use WireMock\Serde\PostNormalizationAmenderInterface;

class WebhookDefinition implements PostNormalizationAmenderInterface
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
        $this->_delay = $delayDistribution->toArray();
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

    public function toArray(): array
    {
        $array = array(
            'method' => $this->_method,
            'url' => $this->_url
        );
        if (isset($this->_headers)) {
            $array['headers'] = $this->_headers;
        }
        if (isset($this->_body)) {
            $array['body'] = $this->_body;
        }
        if (isset($this->_base64Body)) {
            $array['base64Body'] = $this->_base64Body;
        }
        if (isset($this->_delay)) {
            $array['delay'] = $this->_delay;
        }
        return array_merge(
            $array,
            $this->_extraParameters ?? array()
        );
    }

    public static function fromArray(array $array): self
    {
        $webhook = new self();

        if (isset($array['method'])) {
            $webhook->_method = $array['method'];
            unset($array['method']);
        }

        if (isset($array['url'])) {
            $webhook->_url = $array['url'];
            unset($array['url']);
        }

        if (isset($array['headers'])) {
            $webhook->_headers = $array['headers'];
            unset($array['headers']);
        }

        if (isset($array['body'])) {
            $webhook->_body = $array['body'];
            unset($array['body']);
        }

        if (isset($array['base64Body'])) {
            $webhook->_base64Body = $array['base64Body'];
            unset($array['base64Body']);
        }

        if (isset($array['delay'])) {
            $webhook->_delay = $array['delay'];
            unset($array['delay']);
        }

        if (!empty($array)) {
            $webhook->_extraParameters = $array;
        }

        return $webhook;
    }

    public static function amendNormalisation(array $normalisedArray, $object): array
    {
        NormalizerUtils::inline($normalisedArray, 'extraParameters');
        return $normalisedArray;
    }
}