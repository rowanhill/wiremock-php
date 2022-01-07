<?php

namespace WireMock\Client;

use WireMock\HamcrestTestCase;

class ResponseDefinitionBuilderTest extends HamcrestTestCase
{
    public function testDefault200StatusIsAvailableInArray()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getStatus(), equalTo(200));
    }

    public function testSpecifiedStatusIsAvailableInArray()
    {
        // given
        $status = 403;
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->withStatus($status);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getStatus(), equalTo($status));
    }

    public function testStatusMessageIsAvailableInArrayIfSet()
    {
        // given
        $statusMessage = "hello there";
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->withStatusMessage($statusMessage);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getStatusMessage(), equalTo($statusMessage));
    }

    public function testStatusMessageIsNotAvailableInArrayIfNotSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getStatusMessage(), nullValue());
    }

    public function testBodyIsAvailableInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $body = '<h1>Some body!</h1>';
        $responseDefinitionBuilder->withBody($body);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getBody(), equalTo($body));
    }

    public function testBodyIsNotAvailableInArrayIfNotSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getBody(), nullValue());
    }

    public function testBodyFileIsAvailableInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $bodyFile = 'someFile';
        $responseDefinitionBuilder->withBodyFile($bodyFile);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getBodyFileName(), equalTo($bodyFile));
    }

    public function testBodyFileIsNotAvailableInArrayIfNotSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getBodyFileName(), nullValue());
    }

    public function testBase64BodyIsAvailableInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $bodyData = 'data';
        $responseDefinitionBuilder->withBodyData($bodyData);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        $base64 = base64_encode($bodyData);
        assertThat($responseDefinition->getBase64Body(), equalTo($base64));
    }

    public function testHeaderIsAvailableInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->withHeader('foo1', 'bar1');
        $responseDefinitionBuilder->withHeader('foo2', 'bar2');

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getHeaders(), equalTo(array('foo1' => 'bar1', 'foo2' => 'bar2')));
    }

    public function testHeaderIsAvailableInArrayAsArrayIfSetMultipleTimes()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->withHeader('foo', 'bar1');
        $responseDefinitionBuilder->withHeader('foo', 'bar2');

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getHeaders(), equalTo(array('foo' => array('bar1', 'bar2'))));
    }

    public function testHeaderIsNotAvailableInArrayIfNotSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getHeaders(), equalTo([]));
    }

    public function testProxyBaseUrlIsAvailableIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->proxiedFrom('http://otherhost.com/approot');

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getProxyBaseUrl(), equalTo('http://otherhost.com/approot'));
    }

    public function testProxyAdditionalHeadersIsNotInArrayIfEmpty()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getProxyBaseUrl(), nullValue());
    }

    public function testProxiedBuilderRetainsMatchersAddedSoFar()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder = $responseDefinitionBuilder
            ->withStatus(404)
            ->withHeader('X-Header', 'four oh four')
            ->proxiedFrom('foo');

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getStatus(), equalTo(404));
        assertThat($responseDefinition->getHeaders(), equalTo(array('X-Header' => 'four oh four')));
        assertThat($responseDefinition->getProxyBaseUrl(), equalTo('foo'));
    }

    public function testProxyAdditionalHeadersIsInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder = $responseDefinitionBuilder
            ->proxiedFrom('foo')
            ->withAdditionalRequestHeader('X-Header', 'val');

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getAdditionalProxyRequestHeaders(), equalTo(array('X-Header' => 'val')));
    }

    public function testFixedDelayMillisecondsIsInArrayIfSet()
    {
        // given
        $responseDefinitionBuilder = new ResponseDefinitionBuilder();
        $responseDefinitionBuilder->withFixedDelay(2000);

        // when
        $responseDefinition = $responseDefinitionBuilder->build();

        // then
        assertThat($responseDefinition->getFixedDelayMillis(), equalTo(2000));
    }
}
