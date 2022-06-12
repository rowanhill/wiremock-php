<?php

use PHPUnit\Framework\Assert;
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

    /*
     * assertEquals gives good diffing of differences, so we want to use it as our assertion, but does considers
     * empty arrays not equal to null.
     *
     * We want to treat empty arrays as equal to null, as the local stub mapping will frequently have empty
     * array values, which will be dropped in serializing (both in data to and from WireMock), and will show
     * as null in the version returned from the server.
     *
     * As a compromise (aka "hack"), we first check the non-strict equality with PHP's != operator. This *does* consider
     * empty arrays and null to be the same. Only if this test fails do we use assertEquals (to avoid false failures).
     *
     * As an extra safety check, we also use Hamcrest's equalTo, which considers empty arrays equal to null.
     */
    /** @noinspection PhpNonStrictObjectEqualityInspection */
    if ($mappingsFromServer[0] != $localStubMapping) {
        Assert::assertEquals($localStubMapping, $mappingsFromServer[0]);
    }
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
