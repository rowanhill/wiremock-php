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
    private $_status = 200;
    /** @var string */
    private $_statusMessage;
    /** @var string */
    private $_body;
    /** @var string */
    private $_bodyFileName;
    /** @var string */
    private $_base64Body;
    /** @var array */
    private $_headers;
    /** @var string */
    private $_proxyBaseUrl;
    /** @var array */
    private $_additionalProxyRequestHeaders;
    /** @var int */
    private $_fixedDelayMillis;
    /** @var DelayDistribution */
    protected $_randomDelayDistribution;
    /** @var ChunkedDribbleDelay */
    protected $_chunkedDribbleDelay;
    /** @var string */
    private $_fault;
    /** @var string[] */
    private $_transformers = array();
    /** @var array */
    private $_transformerParameters = array();
    /** @var string */
    private $_proxyUrlPrefixToRemove;

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
        $this->_status = $status;
        $this->_statusMessage = $statusMessage;
        $this->_body = $body;
        $this->_bodyFileName = $bodyFile;
        $this->_base64Body = $base64Body;
        $this->_headers = $headers;
        $this->_proxyBaseUrl = $proxyBaseUrl;
        $this->_fixedDelayMillis = $fixedDelayMillis;
        $this->_randomDelayDistribution = $randomDelayDistribution;
        $this->_chunkedDribbleDelay = $chunkedDribbleDelay;
        $this->_fault = $fault;
        $this->_transformers = $transformers;
        $this->_transformerParameters = $transformerParameters;
        $this->_additionalProxyRequestHeaders = $additionalProxyRequestHeaders;
        $this->_proxyUrlPrefixToRemove = $proxyUrlPrefixToRemove;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->_statusMessage;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @return string
     */
    public function getBodyFileName()
    {
        return $this->_bodyFileName;
    }

    /**
     * @return string
     */
    public function getBase64Body()
    {
        return $this->_base64Body;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return string
     */
    public function getProxyBaseUrl()
    {
        return $this->_proxyBaseUrl;
    }

    /**
     * @return array
     */
    public function getAdditionalProxyRequestHeaders()
    {
        return $this->_additionalProxyRequestHeaders;
    }

    /**
     * @return int
     */
    public function getFixedDelayMillis()
    {
        return $this->_fixedDelayMillis;
    }

    /**
     * @return DelayDistribution
     */
    public function getRandomDelayDistribution()
    {
        return $this->_randomDelayDistribution;
    }

    /**
     * @return ChunkedDribbleDelay
     */
    public function getChunkedDribbleDelay()
    {
        return $this->_chunkedDribbleDelay;
    }

    /**
     * @return string
     */
    public function getFault()
    {
        return $this->_fault;
    }

    /**
     * @return string[]
     */
    public function getTransformers()
    {
        return $this->_transformers;
    }

    /**
     * @return array
     */
    public function getTransformerParameters()
    {
        return $this->_transformerParameters;
    }

    /**
     * @return string
     */
    public function getProxyUrlPrefixToRemove()
    {
        return $this->_proxyUrlPrefixToRemove;
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
            $object->_randomDelayDistribution = $delayDistrib;
        }

        return new ObjectToPopulateResult($object, $normalisedArray);
    }
}
