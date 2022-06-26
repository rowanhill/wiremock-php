<?php

namespace WireMock\Recording;

use WireMock\Client\WireMock;
use WireMock\HamcrestTestCase;
use WireMock\Serde\SerializerFactory;

class RecordSpecBuildTest extends HamcrestTestCase
{
    private $_serializer;

    protected function setUp(): void
    {
        $this->_serializer = SerializerFactory::default();
    }

    private function toArray($obj)
    {
        return $this->_serializer->normalize($obj);
    }

    public function testTargetIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->forTarget('foo')
            ->build());

        // then
        assertThat($array, hasEntry('targetBaseUrl', 'foo'));
    }

    public function testRequestPatternIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->onlyRequestsMatching(WireMock::getRequestedFor(WireMock::urlEqualTo('foo')))
            ->build());

        // then
        assertThat($array, hasEntry('filters', array(
            'method' => 'GET',
            'url' => 'foo'
        )));
    }

    public function testRequestIdsAreIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->onlyRequestIds(['123', '456'])
            ->build());

        // then
        assertThat($array, hasEntry('filters', array(
            'ids' => ['123', '456']
        )));
    }

    public function testCaptureHeadersAreIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->captureHeader('Accept')
            ->captureHeader('Content-Type', true)
            ->build());

        // then
        assertThat($array, hasEntry('captureHeaders', array(
            'Accept' => [],
            'Content-Type' => array('caseInsensitive' => true)
        )));
    }

    public function testCaptureHeadersWithoutCaseInsensitiveAreSerializedToEmptyObject()
    {
        // given
        $spec = WireMock::recordSpec()->captureHeader('Accept')->build();

        // when
        $json = $this->_serializer->serialize($spec);

        // then
        assertThat($json, equalTo('{"captureHeaders":{"Accept":[]},"persist":true,"repeatsAsScenarios":true}'));
    }

    public function testBinaryBodySizeExtractThresholdIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->extractBinaryBodiesOver(10240)
            ->build());

        // then
        assertThat($array, hasEntry('extractBodyCriteria', array(
            'binarySizeThreshold' => '10240'
        )));
    }

    public function testTextBodySizeExtractThresholdIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->extractTextBodiesOver(2048)
            ->build());

        // then
        assertThat($array, hasEntry('extractBodyCriteria', array(
            'textSizeThreshold' => '2048'
        )));
    }

    public function testPersistenceIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->makeStubsPersistent(false)
            ->build());

        // then
        assertThat($array, hasEntry('persist', false));
    }

    public function testIgnoringRepeatsIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->ignoreRepeatRequests()
            ->build());

        // then
        assertThat($array, hasEntry('repeatsAsScenarios', false));
    }

    public function testTransformersAreIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->transformers('modify-response-header')
            ->build());

        // then
        assertThat($array, hasEntry('transformers', array('modify-response-header')));
    }

    public function testTransformerParemetersAreIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->transformerParameters(array('headerValue' => '123'))
            ->build());

        // then
        assertThat($array, hasEntry('transformerParameters', array('headerValue' => '123')));
    }

    public function testEqualToJsonRequestBodyPatternIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->matchRequestBodyWithEqualToJson(false, true)
            ->build());

        // then
        assertThat($array, hasEntry('requestBodyPattern', array(
            'matcher' => 'equalToJson',
            'ignoreArrayOrder' => false,
            'ignoreExtraElements' => true
        )));
    }

    public function testEqualToXmlRequestBodyPatternIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->matchRequestBodyWithEqualToXml()
            ->build());

        // then
        assertThat($array, hasEntry('requestBodyPattern', array(
            'matcher' => 'equalToXml'
        )));
    }

    public function testEqualToRequestBodyPatternIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->matchRequestBodyWithEqualTo(true)
            ->build());

        // then
        assertThat($array, hasEntry('requestBodyPattern', array(
            'matcher' => 'equalTo',
            'caseInsensitive' => true
        )));
    }

    public function testAutoRequestBodyPatternIsIncludedInArray()
    {
        // when
        $array = $this->toArray(WireMock::recordSpec()
            ->chooseBodyMatchTypeAutomatically(true, true, true)
            ->build());

        // then
        assertThat($array, hasEntry('requestBodyPattern', array(
            'matcher' => 'auto',
            'caseInsensitive' => true,
            'ignoreArrayOrder' => true,
            'ignoreExtraElements' => true
        )));
    }
}