<?php

use WireMock\Stubbing\StubMapping;

function assertThatTheOnlyMappingPresentIs(StubMapping $stubMapping)
{
    $mappings = getMappings();
    assertThat($mappings, is(arrayWithSize(1)));
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
