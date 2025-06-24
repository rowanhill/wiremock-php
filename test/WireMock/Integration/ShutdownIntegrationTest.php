<?php

namespace WireMock\Integration;

/**
 * @group integration
 */
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
