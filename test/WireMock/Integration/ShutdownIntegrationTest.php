<?php

namespace WireMock\Integration;

class ShutdownIntegrationTest extends WireMockIntegrationTest
{
    public function testShutdownTerminatesStandaloneServer()
    {
        // when
        self::$_wireMock->shutdownServer();

        // then
        assertThat(self::$_wireMock->isShutDown(), is(true));
    }
}
