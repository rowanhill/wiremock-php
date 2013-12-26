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
        exec('cd ../wiremock && `java -jar wiremock-1.33-standalone.jar &> wiremock.log &`');
        self::$_wireMock = WireMock::create();
        assertThat(self::$_wireMock->isAlive(), is(true));
    }

    static function tearDownAfterClass()
    {
        $result = 0;
        $output = array();
        exec(
            "kill -9 `ps -e | grep \"java -jar wiremock-1.33-standalone.jar\" | grep -v grep | awk '{print $1}'`",
            $output,
            $result
        );
        assertThat($result, is(0));
    }

    function setUp()
    {
        $this->_testClient = new TestClient('localhost', 8080);
        self::$_wireMock->reset();
    }
}