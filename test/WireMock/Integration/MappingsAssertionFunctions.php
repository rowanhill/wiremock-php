<?php

use WireMock\Client\ListStubMappingsResult;
use WireMock\Stubbing\StubMapping;

function assertThatTheOnlyMappingPresentIs(StubMapping $localStubMapping)
{
    $mappingsFromServer = getMappings();
    assertThat($mappingsFromServer, is(arrayWithSize(1)));

    $serverStubMappingArray = $mappingsFromServer[0]->toArray();
    $localStubMappingArray = $localStubMapping->toArray();

    if (!isset($localStubMappingArray['request']['method'])) {
        // If we didn't set a request method in the stub, the server will have returned ANY as the method, causing the
        // local and server mapping arrays not to match, unless we delete the server-returned method
        unset($serverStubMappingArray['request']['method']);
    }

    assertThat($serverStubMappingArray, is($localStubMappingArray));
}

function assertThatThereAreNoMappings()
{
    $mappings = getMappings();
    assertThat($mappings, is(emptyArray()));
}

function getMappings()
{
    $adminJson = file_get_contents('http://localhost:8080/__admin');
    $resultArray = json_decode($adminJson, true);
    $result = new ListStubMappingsResult($resultArray);
    return $result->getMappings();
}
