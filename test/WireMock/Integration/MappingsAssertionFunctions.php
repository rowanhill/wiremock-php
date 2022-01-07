<?php

use WireMock\Serde\SerializerFactory;
use WireMock\Stubbing\StubMapping;

/**
 * @param StubMapping $localStubMapping
 * @param callable[] $expectedTransformations
 */
function assertThatTheOnlyMappingPresentIs(StubMapping $localStubMapping, $expectedTransformations = array())
{
    $serializer = SerializerFactory::default();

    $mappingsFromServer = getMappings();
    assertThat($mappingsFromServer, is(arrayWithSize(1)));

    $serverStubMappingArray = $serializer->normalize($mappingsFromServer[0], 'json');
    $localStubMappingArray = $serializer->normalize($localStubMapping, 'json');

    if (!isset($localStubMappingArray['request']['method'])) {
        // If we didn't set a request method in the stub, the server will have returned ANY as the method, causing the
        // local and server mapping arrays not to match, unless we delete the server-returned method
        unset($serverStubMappingArray['request']['method']);
    }
    foreach ($expectedTransformations as $transformation) {
        $transformation($localStubMappingArray);
    }

    assertThat($serverStubMappingArray, equalTo($localStubMappingArray));
}

function assertThatThereAreNoMappings()
{
    $mappings = getMappings();
    assertThat($mappings, is(emptyArray()));
}

function getMappings()
{
    $defaultSerializer = SerializerFactory::default();
    $adminJson = file_get_contents('http://localhost:8080/__admin/mappings');
    // TODO: Move to single deserialize step when ListStubMappingsResult is serializer-friendly
    $resultArray = $defaultSerializer->decode($adminJson, 'json');
    return $defaultSerializer->denormalize($resultArray['mappings'], StubMapping::class.'[]', 'json');
}
