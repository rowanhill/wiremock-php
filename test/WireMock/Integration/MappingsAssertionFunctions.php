<?php

use WireMock\Stubbing\StubMapping;

function assertThatTheOnlyMappingPresentIs(StubMapping $stubMapping)
{
    $mappings = getMappings();
    assertThat($mappings, is(arrayWithSize(1)));

    $stubMappingArray = $stubMapping->toArray();

    // If the stub mapping didn't include an ID, we don't want to match on what WireMock auto generated. If the stubbing
    // *did* include an ID, we should match on that, too.
    if (!$stubMappingArray['id']) {
        unset($mappings[0]['id']);
    }

    unset($mappings[0]['uuid']);

    assertThat($mappings[0], is($stubMapping->toArray()));
}

function assertThatThereAreNoMappings()
{
    $mappings = getMappings();
    assertThat($mappings, is(emptyArray()));
}

function getMappings()
{
    $adminJson = file_get_contents('http://localhost:8080/__admin');
    $admin = json_decode($adminJson, true);
    return $admin['mappings'];
}
