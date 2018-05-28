<?php

namespace WireMock\Client;

class ResponseDefinitionBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault200StatusIsAvailableInArray()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // then
        assertThat($responseDefArray, hasEntry('status', 200));
    }

    public function testSpecifiedStatusIsAvailableInArray()
    {
        // given
        $status = 403;
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->withStatus($status);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // then
        assertThat($responseDefArray, hasEntry('status', $status));
    }

    public function testStatusMessageIsAvailableInArrayIfSet()
    {
        // given
        $statusMessage = "hello there";
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->withStatusMessage($statusMessage);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // then
        assertThat($responseDefArray, hasEntry('statusMessage', $statusMessage));
    }

    public function testStatusMessageIsNotAvailableInArrayIfNotSet()
    {
        // given
        $statusMessage = "hello there";
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // then
        assertThat($responseDefArray, not(hasKey('statusMessage')));
    }

    public function testBodyIsAvailableInArrayIfSet()
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

    public function testBodyIsNotAvailableInArrayIfNotSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, not(hasKey('body')));
    }

    public function testBodyFileIsAvailableInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $bodyFile = 'someFile';
        $responseDefinitionBuilder->withBodyFile($bodyFile);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, hasEntry('bodyFileName', $bodyFile));
    }

    public function testBodyFileIsNotAvailableInArrayIfNotSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, not(hasKey('bodyFileName')));
    }

    public function testBase64BodyIsAvailableInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $bodyData = 'data';
        $responseDefinitionBuilder->withBodyData($bodyData);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        $base64 = base64_encode($bodyData);
        assertThat($responseDefArray, hasEntry('base64Body', $base64));
    }

    public function testHeaderIsAvailableInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->withHeader('foo1', 'bar1');
        $responseDefinitionBuilder->withHeader('foo2', 'bar2');

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, hasEntry('headers', array('foo1' => 'bar1', 'foo2' => 'bar2')));
    }

    public function testHeaderIsNotAvailableInArrayIfNotSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, not(hasKey('headers')));
    }

    public function testProxyBaseUrlIsAvailableIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->proxiedFrom('http://otherhost.com/approot');

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, hasEntry('proxyBaseUrl', 'http://otherhost.com/approot'));
    }

    public function testFixedDelayMillisecondsIsInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->withFixedDelay(2000);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();
        $responseDefArray = $responseDefinition->toArray();

        // when
        assertThat($responseDefArray, hasEntry('fixedDelayMilliseconds', 2000));
    }
}
