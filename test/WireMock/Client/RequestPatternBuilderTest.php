<?php

namespace WireMock\Client;

use WireMock\HamcrestTestCase;
use WireMock\Matching\CustomMatcherDefinition;
use WireMock\Matching\UrlMatchingStrategy;

class RequestPatternBuilderTest extends HamcrestTestCase
{
    public function testMethodAndUrlMatchingStrategyAreInArray()
    {
        // given
        $method = 'GET';
        $matchingStrategy = new UrlMatchingStrategy('url', '/some/url');
        $requestPatternBuilder = new RequestPatternBuilder($method, $matchingStrategy);

        // when
        $requestPattern = $requestPatternBuilder->build();

        // then
        assertThat($requestPattern->getMethod(), is($method));
        assertThat($requestPattern->getUrlMatchingStrategy(), is($matchingStrategy));
    }

    public function testHeaderWithValueMatchingStrategyIsInArrayIfSpecified()
    {
        // given
        $requestPatternBuilder = new RequestPatternBuilder('GET', WireMock::urlEqualTo('/some/url'));
        $matchingStrategy = WireMock::equalTo('something');

        // when
        $requestPatternBuilder->withHeader('Some-Header', $matchingStrategy);
        $requestPattern = $requestPatternBuilder->build();

        // then
        assertThat($requestPattern->getHeaders(), hasEntry('Some-Header', $matchingStrategy));
    }

    public function testHeaderAbsenceIsInArrayIfSpecified()
    {
        // given
        $requestPatternBuilder = new RequestPatternBuilder('GET', WireMock::urlEqualTo('/some/url'));
        $matchingStrategy = new ValueMatchingStrategy('absent', true);

        // when
        $requestPatternBuilder->withoutHeader('Some-Header');
        $requestPattern = $requestPatternBuilder->build();

        // then
        assertThat($requestPattern->getHeaders(), hasEntry('Some-Header', $matchingStrategy));
    }

    public function testCookieWithValueMatchingStrategyIsInArrayIfSpecified()
    {
        // given
        $requestPatternBuilder = new RequestPatternBuilder('GET', WireMock::urlEqualTo('/some/url'));
        $matchingStrategy = WireMock::equalTo('something');

        // when
        $requestPatternBuilder->withCookie('aCookie', $matchingStrategy);
        $requestPattern = $requestPatternBuilder->build();

        // then
        assertThat($requestPattern->getCookies(), hasEntry('aCookie', $matchingStrategy));
    }

    public function testRequestBodyPatternsAreInArrayIfSpecified()
    {
        // given
        $requestPatternBuilder = new RequestPatternBuilder('GET', WireMock::urlEqualTo('/some/url'));
        $matchingStrategy = new ValueMatchingStrategy('equalTo', 'aValue');

        // when
        $requestPatternBuilder->withRequestBody($matchingStrategy);
        $requestPattern = $requestPatternBuilder->build();

        // then
        assertThat($requestPattern->getBodyPatterns(), hasItem($matchingStrategy));
    }

    public function testBasicAuthIsInArrayIfSpecified()
    {
        // given
        $requestPatternBuilder = new RequestPatternBuilder('GET', WireMock::urlEqualTo('/some/url'));

        // when
        $requestPatternBuilder->withBasicAuth('uname', 'pword');
        $requestPattern = $requestPatternBuilder->build();

        // then
        assertThat($requestPattern->getBasicAuthCredentials(), equalTo(
            new BasicCredentials('uname', 'pword')));
    }

    public function testBuilderCanBeCreatedWithCustomMatcherNameAndParams()
    {
        // when
        $customMatcherName = 'custom-matcher';
        $params = array('param' => 'val');
        $builder = new RequestPatternBuilder($customMatcherName, $params);
        $pattern = $builder->build();

        // then
        assertThat($pattern->getMethod(), nullValue());
        assertThat($pattern->getUrlMatchingStrategy(), nullValue());
        assertThat($pattern->getCustomMatcher(), equalTo(new CustomMatcherDefinition($customMatcherName, $params)));
    }

    public function testCustomMatcherDefinitionIsInArrayIfSpecified()
    {
        // given
        $builder = new RequestPatternBuilder('GET', WireMock::urlEqualTo('/some/url'));
        $customMatcherName = 'custom-matcher';
        $params = array('param' => 'val');

        // when
        $builder->withCustomMatcher($customMatcherName, $params);
        $pattern = $builder->build();

        // then
        assertThat($pattern->getCustomMatcher(), equalTo(new CustomMatcherDefinition($customMatcherName, $params)));
    }
}
