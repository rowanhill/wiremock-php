<?php

namespace WireMock\Http;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Serializer;
use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;
use WireMock\Fault\DelayDistributionFactory;
use WireMock\Fault\LogNormal;
use WireMock\Fault\UniformDistribution;
use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\NormalizerUtils;
use WireMock\Serde\ObjectToPopulateFactoryInterface;
use WireMock\Serde\ObjectToPopulateResult;
use WireMock\Serde\PostNormalizationAmenderInterface;
use WireMock\Serde\PreDenormalizationAmenderInterface;

class ResponseDefinition implements PostNormalizationAmenderInterface, PreDenormalizationAmenderInterface, ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var int */
    private $status = 200;
    /** @var string */
    private $statusMessage;
    /** @var string */
    private $body;
    /** @var string */
    private $bodyFileName;
    /** @var string */
    private $base64Body;
    /** @var array */
    private $headers;
    /** @var string */
    private $proxyBaseUrl;
    /** @var array */
    private $additionalProxyRequestHeaders;
    /** @var int */
    private $fixedDelayMillis;
    /** @var DelayDistribution */
    protected $randomDelayDistribution;
    /** @var ChunkedDribbleDelay */
    protected $chunkedDribbleDelay;
    /** @var string */
    private $fault;
    /** @var string[] */
    private $transformers = array();
    /** @var array */
    private $transformerParameters = array();
    /** @var string */
    private $proxyUrlPrefixToRemove;

    /**
     * ResponseDefinition constructor.
     * @param int $status
     * @param string $statusMessage
     * @param string $body
     * @param string $bodyFile
     * @param string $base64Body
     * @param array $headers
     * @param string $proxyBaseUrl
     * @param array $additionalProxyRequestHeaders
     * @param int $fixedDelayMillis
     * @param DelayDistribution $randomDelayDistribution
     * @param ChunkedDribbleDelay $chunkedDribbleDelay
     * @param string $fault
     * @param string[] $transformers
     * @param array $transformerParameters
     */
    public function __construct(
        $status,
        $statusMessage = null,
        $body = null,
        $bodyFile = null,
        $base64Body = null,
        $headers = null,
        $proxyBaseUrl = null,
        $additionalProxyRequestHeaders = null,
        $fixedDelayMillis = null,
        $randomDelayDistribution = null,
        $chunkedDribbleDelay = null,
        $fault = null,
        $transformers = null,
        $transformerParameters = null,
        $proxyUrlPrefixToRemove = null
    ) {
        $this->status = $status;
        $this->statusMessage = $statusMessage;
        $this->body = $body;
        $this->bodyFileName = $bodyFile;
        $this->base64Body = $base64Body;
        $this->headers = $headers;
        $this->proxyBaseUrl = $proxyBaseUrl;
        $this->fixedDelayMillis = $fixedDelayMillis;
        $this->randomDelayDistribution = $randomDelayDistribution;
        $this->chunkedDribbleDelay = $chunkedDribbleDelay;
        $this->fault = $fault;
        $this->transformers = $transformers;
        $this->transformerParameters = $transformerParameters;
        $this->additionalProxyRequestHeaders = $additionalProxyRequestHeaders;
        $this->proxyUrlPrefixToRemove = $proxyUrlPrefixToRemove;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getBodyFileName()
    {
        return $this->bodyFileName;
    }

    /**
     * @return string
     */
    public function getBase64Body()
    {
        return $this->base64Body;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getProxyBaseUrl()
    {
        return $this->proxyBaseUrl;
    }

    /**
     * @return array
     */
    public function getAdditionalProxyRequestHeaders()
    {
        return $this->additionalProxyRequestHeaders;
    }

    /**
     * @return int
     */
    public function getFixedDelayMillis()
    {
        return $this->fixedDelayMillis;
    }

    /**
     * @return DelayDistribution
     */
    public function getRandomDelayDistribution()
    {
        return $this->randomDelayDistribution;
    }

    /**
     * @return ChunkedDribbleDelay
     */
    public function getChunkedDribbleDelay()
    {
        return $this->chunkedDribbleDelay;
    }

    /**
     * @return string
     */
    public function getFault()
    {
        return $this->fault;
    }

    /**
     * @return string[]
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * @return array
     */
    public function getTransformerParameters()
    {
        return $this->transformerParameters;
    }

    /**
     * @return string
     */
    public function getProxyUrlPrefixToRemove()
    {
        return $this->proxyUrlPrefixToRemove;
    }

    public static function amendPostNormalisation(array $normalisedArray, $object): array
    {
        NormalizerUtils::renameKey($normalisedArray, 'fixedDelayMillis', 'fixedDelayMilliseconds');
        NormalizerUtils::renameKey($normalisedArray, 'randomDelayDistribution', 'delayDistribution');
        return $normalisedArray;
    }

    public static function amendPreNormalisation(array $normalisedArray): array
    {
        NormalizerUtils::renameKey($normalisedArray, 'fixedDelayMilliseconds', 'fixedDelayMillis');
        NormalizerUtils::renameKey($normalisedArray, 'delayDistribution', 'randomDelayDistribution');
        return $normalisedArray;
    }

    public static function createObjectToPopulate(array $normalisedArray, Serializer $serializer, string $format, array $context): ObjectToPopulateResult
    {
        $object = new self(0);

        // Cannot automatically deserialize DelayDistribution, because it's an interface (and type discrimination
        // described in Serializer docs doesn't seem to work)
        if (isset($normalisedArray['randomDelayDistribution'])) {
            $delayDistribArray = $normalisedArray['randomDelayDistribution'];
            unset($normalisedArray['randomDelayDistribution']);
            $type = $delayDistribArray['type'];
            unset($delayDistribArray['type']);
            if ($type === 'lognormal') {
                $delayDistrib = $serializer->denormalize($delayDistribArray, LogNormal::class, $format, $context);
            } else if ($type === 'uniform') {
                $delayDistrib = $serializer->denormalize($delayDistribArray, UniformDistribution::class, $format, $context);
            } else {
                throw new NotNormalizableValueException("Unknown DelayDistribution type '$type'");
            }
            $object->randomDelayDistribution = $delayDistrib;
        }

        return new ObjectToPopulateResult($object, $normalisedArray);
    }
}
