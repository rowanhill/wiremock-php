<?php

namespace WireMock\Client;

require_once 'WireMockIntegrationTest.php';

class ProxyingIntegrationTest extends WireMockIntegrationTest
{
    function testProxyBaseUrlOfStubCanBeSet() {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()->proxiedFrom('http://otherhost.com/approot'))
        );

        // then
        $stubMappingArray = $stubMapping->toArray();
        assertThat($stubMappingArray['response']['proxyBaseUrl'], is('http://otherhost.com/approot'));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }
}