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
use WireMock\SerdeGen\SerdeTypeLookupFactory;
use WireMock\Stubbing\Scenario;
use WireMock\Stubbing\StubMapping;

class SerializerFactory
{
    /**
     * @throws \ReflectionException
     * @throws SerializationException
     */
    public static function default()
    {
        // TODO: Instead of generating this at run time, deserialize an already generated version
        $lookup = SerdeTypeLookupFactory::createLookup(
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
            EqualToMatchingStrategy::class,
            JsonPathValueMatchingStrategy::class,
            DateTimeMatchingStrategy::class,
            LogicalOperatorMatchingStrategy::class,
            FixedDelay::class
        );

        return new Serializer($lookup);
    }
}