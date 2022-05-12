<?php

use WireMock\Client\ListStubMappingsResult;
use WireMock\Serde\SerializerFactory;
use WireMock\Stubbing\StubMapping;

/**
 * @param StubMapping $localStubMapping
 */
function assertThatTheOnlyMappingPresentIs(StubMapping $localStubMapping)
{
    $mappingsFromServer = getMappings();
    assertThat($mappingsFromServer, is(arrayWithSize(1)));

    assertThat($mappingsFromServer[0], equalTo($localStubMapping));
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
    /** @var ListStubMappingsResult $listResult */
    $listResult = $defaultSerializer->deserialize($adminJson, ListStubMappingsResult::class);
    return $listResult->getMappings();
}
