<?php

namespace WireMock\Client;

class ResponseDefinitionBuilderTest extends \PHPUnit_Framework_TestCase
{
    function testDefault200StatusIsAvailableInArray()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // then
        assertThat($responseDefArray, hasEntry('status', 200));
    }

    function testBodyIsAvailableInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $body = '<h1>Some body!</h1>';
        $responseDefinitionBuilder->withBody($body);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, hasEntry('body', $body));
    }

    function testBodyIsNotAvailableInArrayIfNotSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, not(hasKey('body')));
    }
}