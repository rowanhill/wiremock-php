<?php

namespace WireMock\Serde;

use WireMock\Client\BasicCredentials;
use WireMock\Client\CountMatchingRequestsResult;
use WireMock\Client\DateTimeMatchingStrategy;
use WireMock\Client\EqualToMatchingStrategy;
use WireMock\Client\EqualToXmlMatchingStrategy;
use WireMock\Client\FindNearMissesResult;
use WireMock\Client\FindRequestsResult;
use WireMock\Client\GetScenariosResult;
use WireMock\Client\GetServeEventsResult;
use WireMock\Client\JsonPathValueMatchingStrategy;
use WireMock\Client\JsonValueMatchingStrategy;
use WireMock\Client\ListStubMappingsResult;
use WireMock\Client\LoggedRequest;
use WireMock\Client\LoggedResponse;
use WireMock\Client\LogicalOperatorMatchingStrategy;
use WireMock\Client\MatchResult;
use WireMock\Client\Meta;
use WireMock\Client\MultipartValuePattern;
use WireMock\Client\NearMiss;
use WireMock\Client\ServeEvent;
use WireMock\Client\UnmatchedRequests;
use WireMock\Client\ValueMatchingStrategy;
use WireMock\Client\XPathValueMatchingStrategy;
use WireMock\Fault\ChunkedDribbleDelay;
use WireMock\Fault\DelayDistribution;
use WireMock\Fault\FixedDelay;
use WireMock\Fault\LogNormal;
use WireMock\Fault\UniformDistribution;
use WireMock\Http\ResponseDefinition;
use WireMock\Matching\CustomMatcherDefinition;
use WireMock\Matching\RequestPattern;
use WireMock\Matching\UrlMatchingStrategy;
use WireMock\PostServe\PostServeAction;
use WireMock\PostServe\WebhookDefinition;
use WireMock\Recording\RecordingStatusResult;
use WireMock\Recording\SnapshotRecordResult;
use WireMock\Serde\Type\SerdeTypeFactory;
use WireMock\Stubbing\Scenario;
use WireMock\Stubbing\StubMapping;

class Serializer
{
    /** @var PropertyMapCache */
    private $propertyMapCache;
    /** @var SerdeTypeFactory */
    private $serdeTypeFactory;

    public function __construct()
    {
        $this->propertyMapCache = new PropertyMapCache(
            StubMapping::class,
            RequestPattern::class,
            ResponseDefinition::class,
            PostServeAction::class,
            UrlMatchingStrategy::class,
            ValueMatchingStrategy::class,
            MultipartValuePattern::class,
            BasicCredentials::class,
            CustomMatcherDefinition::class,
            DelayDistribution::class,
            LogNormal::class,
            UniformDistribution::class,
            ChunkedDribbleDelay::class,
            WebhookDefinition::class,
            ListStubMappingsResult::class,
            Meta::class,
            RecordingStatusResult::class,
            GetServeEventsResult::class,
            ServeEvent::class,
            LoggedRequest::class,
            LoggedResponse::class,
            UnmatchedRequests::class,
            FindNearMissesResult::class,
            SnapshotRecordResult::class,
            FindRequestsResult::class,
            GetScenariosResult::class,
            JsonValueMatchingStrategy::class,
            EqualToXmlMatchingStrategy::class,
            XPathValueMatchingStrategy::class,
            CountMatchingRequestsResult::class,
            NearMiss::class,
            Scenario::class,
            MatchResult::class,
            LogNormal::class,
            UniformDistribution::class,
            EqualToMatchingStrategy::class,
            JsonPathValueMatchingStrategy::class,
            DateTimeMatchingStrategy::class,
            LogicalOperatorMatchingStrategy::class,
            FixedDelay::class
        );
        $this->serdeTypeFactory = new SerdeTypeFactory();
    }

    /**
     * Serializes data object into JSON
     *
     * @param mixed $object object to serialize
     *
     * @return string JSON serialization of object
     */
    public function serialize($object): string
    {
        $normalizedArray = $this->normalize($object);
        return json_encode($normalizedArray, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param mixed $object object to normalize
     * @return mixed An associative array or a primative type
     */
    public function normalize($object)
    {
        if (is_object($object)) {
            $publicMethods = get_class_methods($object);
            $publicGetters = array_filter(
                $publicMethods,
                function($m) use ($object) {
                    if (substr($m, 0, 3) !== 'get' && substr($m, 0, 2) !== 'is') {
                        return false;
                    }
                    $refMethod = new \ReflectionMethod($object, $m);
                    return !$refMethod->isStatic();
                }
            );
            $result = ArrayMapUtils::array_map_assoc(
                function($key) use ($object) {
                    if (substr($key, 0, 3) === 'get') {
                        $newKey = lcfirst(substr($key, 3));
                    } else {
                        $newKey = lcfirst(substr($key, 2));
                    }
                    $value = $object->$key();
                    return [$newKey, $this->normalize($value)];
                },
                array_fill_keys($publicGetters, null)
            );
            if ($object instanceof PostNormalizationAmenderInterface) {
                $result = forward_static_call([get_class($object), 'amendPostNormalisation'], $result, $object);
            }
            foreach ($result as $key => $item) {
                if ((is_array($item) && empty($item)) || is_null($item)) {
                    unset($result[$key]);
                }
            }
        } elseif (is_array($object)) {
            $result = ArrayMapUtils::array_map_assoc(
                function($key, $value) { return [$key, $this->normalize($value)]; },
                $object
            );
        } else {
            $result = $object;
        }

        return $result;
    }

    /**
     * Deserializes JSON into data object of the given type
     *
     * @param string $json
     * @param string $type
     *
     * @return mixed
     * @throws SerializationException
     */
    public function deserialize(string $json, string $type)
    {
        $data = json_decode($json, true);
        return $this->denormalize($data, $type);
    }

    /**
     * @throws SerializationException
     */
    public function denormalize(&$data, string $type)
    {
        $serdeType = $this->serdeTypeFactory->parseTypeString($type, $this->propertyMapCache);
        return $serdeType->denormalize($data, $this);
    }

    // TODO: Move this to it's own class?
    public function getDiscriminatedType(&$data, string $type): string
    {
        if (!is_subclass_of($type, MappingProvider::class)) {
            return $type;
        }
        /** @var ClassDiscriminatorMapping $classDiscriminatorMapping */
        $classDiscriminatorMapping = forward_static_call(array($type, 'getDiscriminatorMapping'));
        $discriminatorName = $classDiscriminatorMapping->getDiscriminatorPropName();
        if (array_key_exists($discriminatorName, $data)) {
            $discriminatorValue = $data[$discriminatorName];
            $map = $classDiscriminatorMapping->getDiscriminatorValueToClassMap();
            if (array_key_exists($discriminatorValue, $map)) {
                return $map[$discriminatorValue];
            }
        }
        return $type;
    }
}