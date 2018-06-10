<?php

namespace WireMock\Recording;

use WireMock\Client\WireMock;

class RecordSpecBuildTest extends \PHPUnit_Framework_TestCase
{
    public function testTargetIsIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->forTarget('foo')
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('targetBaseUrl', 'foo'));
    }

    public function testRequestPatternIsIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->onlyRequestsMatching(WireMock::getRequestedFor(WireMock::urlEqualTo('foo')))
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('filters', array(
            'method' => 'GET',
            'url' => 'foo'
        )));
    }

    public function testCaptureHeadersAreIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->captureHeader('Accept')
            ->captureHeader('Content-Type', true)
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('captureHeaders', array(
            'Accept' => new \stdClass(),
            'Content-Type' => array('caseInsensitive' => true)
        )));
    }

    public function testBinaryBodySizeExtractThresholdIsIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->extractBinaryBodiesOver(10240)
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('extractBodyCriteria', array(
            'binarySizeThreshold' => '10240'
        )));
    }

    public function testTextBodySizeExtractThresholdIsIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->extractTextBodiesOver(2048)
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('extractBodyCriteria', array(
            'textSizeThreshold' => '2048'
        )));
    }

    public function testPersistenceIsIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->makeStubsPersistent(false)
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('persist', false));
    }

    public function testIgnoringRepeatsIsIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->ignoreRepeatRequests()
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('repeatsAsScenarios', false));
    }

    public function testTransformersAreIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->transformers('modify-response-header')
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('transformers', array('modify-response-header')));
    }

    public function testTransformerParemetersAreIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->transformerParameters(array('headerValue' => '123'))
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('transformerParameters', array('headerValue' => '123')));
    }

    public function testEqualToJsonRequestBodyPatternIsIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->matchRequestBodyWithEqualToJson(false, true)
            ->build()->toArray();

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
        $array = WireMock::recordSpec()
            ->matchRequestBodyWithEqualToXml()
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('requestBodyPattern', array(
            'matcher' => 'equalToXml'
        )));
    }

    public function testEqualToRequestBodyPatternIsIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->matchRequestBodyWithEqualTo(true)
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('requestBodyPattern', array(
            'matcher' => 'equalTo',
            'caseInsensitive' => true
        )));
    }

    public function testAutoRequestBodyPatternIsIncludedInArray()
    {
        // when
        $array = WireMock::recordSpec()
            ->chooseBodyMatchTypeAutomatically(true, true, true)
            ->build()->toArray();

        // then
        assertThat($array, hasEntry('requestBodyPattern', array(
            'matcher' => 'auto',
            'caseInsensitive' => true,
            'ignoreArrayOrder' => true,
            'ignoreExtraElements' => true
        )));
    }
}