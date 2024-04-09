<?php
namespace WireMock\Integration;

use PHPUnit\Framework\TestCase;
use WireMock\Client\Authentication\TokenAuthenticator;
use WireMock\Client\Curl;
use WireMock\Client\HttpWait;
use WireMock\Client\WireMock;
use WireMock\Serde\SerializerFactory;

class CloudIntegrationTest extends TestCase
{
    public function testConnectToCloudHost(): void
    {
        $wiremockCloudHost = getenv('WIREMOCK_CLOUD_HOST');
        $wiremockCloudToken = getenv('WIREMOCK_CLOUD_TOKEN');

        if ($wiremockCloudToken === false || $wiremockCloudHost === false) {
            self::markTestSkipped('Env variables WIREMOCK_CLOUD_HOST or WIREMOCK_CLOUD_TOKEN not set');
        }

        $curl = new Curl(new TokenAuthenticator($wiremockCloudToken));
        $client = new WireMock(new HttpWait($curl), $curl, SerializerFactory::default(), $wiremockCloudHost, 443, 'https');

        self::assertTrue($client->isAlive());
    }
}
