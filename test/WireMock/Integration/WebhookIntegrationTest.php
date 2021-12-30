<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;
use WireMock\Http\RequestMethod;

class WebhookIntegrationTest extends WireMockIntegrationTest
{
    public function testWebhookCallbackCanBeRegistered()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlPathEqualTo("/something-async"))
            ->willReturn(WireMock::ok())
            ->withPostServeAction("webhook", WireMock::webhook()
                ->withMethod(RequestMethod::POST)
                ->withUrl("http://my-target-host/callback")
                ->withHeader("Content-Type", "application/json")
                ->withBody("{ \"result\": \"SUCCESS\" }"))
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testWebhookCallbackWithBase64Body()
    {
        // when
        $packedResponseBody = pack('c*', 0x23, 0x59, 0x11);
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlPathEqualTo("/something-async"))
            ->willReturn(WireMock::ok())
            ->withPostServeAction("webhook", WireMock::webhook()
                ->withMethod(RequestMethod::POST)
                ->withUrl("http://my-target-host/callback")
                ->withHeader("Content-Type", "application/json")
                ->withBodyData($packedResponseBody))
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testWebhookCallbackWithFixedDelay()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlPathEqualTo("/delayed"))
            ->willReturn(WireMock::ok())
            ->withPostServeAction("webhook", WireMock::webhook()
                ->withFixedDelay(1000)
                ->withMethod(RequestMethod::GET)
                ->withUrl("http://my-target-host/callback")
            )
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testWebhookCallbackWithUniformRandomDelay()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlPathEqualTo("/delayed"))
            ->willReturn(WireMock::ok())
            ->withPostServeAction("webhook", WireMock::webhook()
                ->withUniformRandomDelay(500, 1000)
                ->withMethod(RequestMethod::GET)
                ->withUrl("http://my-target-host/callback")
            )
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testWebhookCallbackWithTemplatingBasedOnExtraParameter()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlPathEqualTo("/templating"))
            ->willReturn(WireMock::ok())
            ->withPostServeAction("webhook", WireMock::webhook()
                ->withMethod(RequestMethod::GET)
                ->withUrl("http://my-target-host/callback")
                ->withHeader("X-Multi", "{{parameters.one}}")
                ->withExtraParameter("one", "param-one-value")
            )
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }
}