<?php

namespace WireMock\SerdeGen;

use ReflectionException;
use WireMock\Client\CountMatchingRequestsResult;
use WireMock\Client\FindNearMissesResult;
use WireMock\Client\FindRequestsResult;
use WireMock\Client\GetScenariosResult;
use WireMock\Client\GetServeEventsResult;
use WireMock\Client\ListStubMappingsResult;
use WireMock\Client\LoggedRequest;
use WireMock\Client\UnmatchedRequests;
use WireMock\Client\ValueMatchingStrategy;
use WireMock\Matching\RequestPattern;
use WireMock\Recording\RecordingStatusResult;
use WireMock\Recording\RecordSpec;
use WireMock\Recording\SnapshotRecordResult;
use WireMock\Serde\SerializationException;
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
            // Entry point classes (i.e. explicitly passed as classname to deserialize() or object serialize())
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
            RecordSpec::class,
            StubImport::class,

            // findStubsByMetadata
            // removeEventsByStubMetadata
            ValueMatchingStrategy::class,

            // findNearMissesFor
            LoggedRequest::class,

            // verify
            // findNearMissesFor
            // findAll
            // removeServeEvents
            RequestPattern::class
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