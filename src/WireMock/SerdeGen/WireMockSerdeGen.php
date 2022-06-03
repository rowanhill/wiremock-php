<?php

namespace WireMock\SerdeGen;

use ReflectionException;
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
use WireMock\Recording\RecordSpec;
use WireMock\Recording\SnapshotRecordResult;
use WireMock\Serde\SerializationException;
use WireMock\Stubbing\Scenario;
use WireMock\Stubbing\StubImport;
use WireMock\Stubbing\StubMapping;

class WireMockSerdeGen
{
    /**
     * @throws ReflectionException
     * @throws SerializationException
     */
    public static function generateSerializedWiremockSerdeLookup(): string
    {
        $lookup = SerdeTypeLookupFactory::createLookup(
        // Entry point classes (i.e. explicitly passed to deserialize())
            GetServeEventsResult::class,
            UnmatchedRequests::class,
            FindNearMissesResult::class,
            GetScenariosResult::class,
            ListStubMappingsResult::class,
            StubMapping::class,
            RecordingStatusResult::class,
            CountMatchingRequestsResult::class,
            FindRequestsResult::class,
            SnapshotRecordResult::class,

            // Other classes used
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
            Meta::class,
            ServeEvent::class,
            LoggedRequest::class,
            LoggedResponse::class,
            JsonValueMatchingStrategy::class,
            EqualToXmlMatchingStrategy::class,
            XPathValueMatchingStrategy::class,
            NearMiss::class,
            Scenario::class,
            MatchResult::class,
            EqualToMatchingStrategy::class,
            JsonPathValueMatchingStrategy::class,
            DateTimeMatchingStrategy::class,
            LogicalOperatorMatchingStrategy::class,
            FixedDelay::class,
            RecordSpec::class,
            StubImport::class
        );

        // Use native PHP serialization to create a string of binary data
        return serialize($lookup);
    }

    /**
     * @throws ReflectionException
     * @throws SerializationException
     */
    public static function generateAndSaveWireMockSerdeLookup()
    {
        $lookupSerialized = self::generateSerializedWiremockSerdeLookup();

        file_put_contents(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Serde' . DIRECTORY_SEPARATOR . 'lookup',
            $lookupSerialized
        );
    }
}