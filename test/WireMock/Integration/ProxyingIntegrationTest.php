<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;

require_once 'WireMockIntegrationTest.php';

class ProxyingIntegrationTest extends WireMockIntegrationTest
{
    public function testProxyBaseUrlOfStubCanBeSet()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()->proxiedFrom('http://otherhost.com/approot'))
        );

        // then
        $stubMappingArray = $stubMapping->toArray();
        assertThat($stubMappingArray['response']['proxyBaseUrl'], is('http://otherhost.com/approot'));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testAdditionProxiedRequestHeadersCanBeSet()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(
                WireMock::aResponse()->proxiedFrom('http://otherhost.com/approot')
                ->withAdditionalRequestHeader('X-Header', 'val')
            )
        );

        // then
        $stubMappingArray = $stubMapping->toArray();
        assertThat($stubMappingArray['response']['additionalProxyRequestHeaders'], equalTo(array('X-Header' => 'val')));
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }
}
