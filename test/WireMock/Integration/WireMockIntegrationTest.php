<?php

namespace WireMock\Integration;

use WireMock\Client\WireMock;

require_once 'MappingsAssertionFunctions.php';
require_once 'TestClient.php';

class WireMockIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /** @var WireMock */
    protected static $_wireMock;

    /** @var TestClient */
    protected $_testClient;

    static function setUpBeforeClass()
    {
        $result = 0;
        $output = array();
        exec('(./../wiremock/start.sh) > /dev/null 2>&1 &', $output, $result);
        assertThat($result, is(0));
        self::$_wireMock = WireMock::create();
        assertThat(self::$_wireMock->isAlive(), is(true));
    }

    static function tearDownAfterClass()
    {
        $result = 0;
        $output = array();
        exec('(./../wiremock/stop.sh) > /dev/null 2>&1 &', $output, $result);
        assertThat($result, is(0));
    }

    function setUp()
    {
        $this->_testClient = new TestClient();
        self::$_wireMock->reset();
    }
}