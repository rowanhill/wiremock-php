<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;
use WireMock\HamcrestTestCase;

abstract class WireMockIntegrationTest extends HamcrestTestCase
{
    /** @var WireMock */
    protected static $_wireMock;

    /** @var TestClient */
    protected $_testClient;

    public static function setUpBeforeClass(): void
    {
        self::runCmd('./../wiremock/start.sh');
        self::$_wireMock = WireMock::create();
        assertThat(self::$_wireMock->isAlive(), is(true));
    }

    public static function tearDownAfterClass(): void
    {
        self::runCmd('./../wiremock/stop.sh');
    }

    protected static function runCmd($cmd)
    {
        $result = 0;
        $output = array();
        $redirect = EXEC_BLOCKS_ON_OUTPUT ? '> /dev/null' : '';
        exec("($cmd) $redirect 2>&1 &", $output, $result);
        $output = array_map(function ($line) {
            return "\n$line";
        }, $output);
        echo implode("\n", $output);
        assertThat($result, is(0));
    }

    public function setUp(): void
    {
        $this->_testClient = new TestClient();
        self::$_wireMock->reset();
    }

    public function clearMappings()
    {
        exec('rm -f ../wiremock/1/mappings/*');
    }
}
