<?php

namespace WireMock\Http;

class ResponseDefinitionTest extends \PHPUnit_Framework_TestCase
{
    function testDefault200StatusIsAvailableInArray()
    {
        // given
        $responseDefinition = new ResponseDefinition();

        // when
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, hasEntry('status', 200));
    }

    function testBodyIsAvailableInArrayIfSet()
    {
        // given
        $responseDefinition = new ResponseDefinition();
        $body = '<h1>Some body!</h1>';
        $responseDefinition->setBody($body);

        // when
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, hasEntry('body', $body));
    }

    function testBodyIsNotAvailableInArrayIfNotSet()
    {
        // given
        $responseDefinition = new ResponseDefinition();

        // when
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, not(hasKey('body')));
    }
}