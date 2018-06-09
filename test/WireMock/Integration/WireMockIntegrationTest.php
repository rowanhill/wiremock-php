<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;

require_once 'MappingsAssertionFunctions.php';
require_once 'TestClient.php';

abstract class WireMockIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /** @var WireMock */
    protected static $_wireMock;

    /** @var TestClient */
    protected $_testClient;

    public static function setUpBeforeClass()
    {
        self::runCmd('./../wiremock/start.sh');
        self::$_wireMock = WireMock::create();
        assertThat(self::$_wireMock->isAlive(), is(true));
    }

    public static function tearDownAfterClass()
    {
        self::runCmd('./../wiremock/stop.sh');
    }

    private static function runCmd($cmd)
    {
        $result = 0;
        $output = array();
        $redirect = EXEC_BLOCKS_ON_OUTPUT ? '> /dev/null' : '';
        exec("($cmd) $redirect 2>&1 &", $output, $result);
        $output = array_map(function ($line) {
            return "\n$line";
        }, $output);
        echo implode($output, "\n");
        assertThat($result, is(0));
    }

    public function setUp()
    {
        $this->_testClient = new TestClient();
        self::$_wireMock->reset();
    }
    
    public function clearMappings()
    {
        exec('rm -f ../wiremock/1/mappings/*');
    }
}
