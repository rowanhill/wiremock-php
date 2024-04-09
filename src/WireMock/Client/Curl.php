<?php

namespace WireMock\Client;

use WireMock\Client\Authentication\Authenticator;
use WireMock\Client\Authentication\NullAuthenticator;

class Curl
{
    /**
     * @var Authenticator
     */
    private $authenticator;

    public function __construct(Authenticator $authenticator = null)
    {
        $this->authenticator = $authenticator ?? new NullAuthenticator();
    }

    /**
     * @param string $url
     * @return string The response body
     * @throws ClientException
     */
    public function get(string $url): string
    {
        return $this->makeCurlRequest('GET', $url);
    }

    /**
     * @param string $url
     * @param string|null $body The request body
     * @return string The response body
     * @throws ClientException
     */
    public function post(string $url, ?string $body = null): string
    {
        return $this->makeCurlRequest('POST', $url, $body);
    }

    /**
     * @param string $url
     * @param array|string|null $body The request body
     * @return string The response body
     * @throws ClientException
     */
    public function put(string $url, ?string $body = null): string
    {
        return $this->makeCurlRequest('PUT', $url, $body);
    }

    /**
     * @param string $url
     * @return string The response body
     * @throws ClientException
     */
    public function delete(string $url): string
    {
        return $this->makeCurlRequest('DELETE', $url);
    }

    /**
     * @throws ClientException
     */
    private function makeCurlRequest(string $method, string $url, ?string $json = null)
    {
        $ch = curl_init($this->authenticator->modifyUrl($url));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($json !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            $contentLength = strlen($json);
        } else {
            $contentLength = 0;
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $defaultHeaders = [
            'Content-Type: application/json',
            sprintf('Content-Length: %s', $contentLength),
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->authenticator->modifyHeaders($defaultHeaders));

        $result = curl_exec($ch);

        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($responseCode < 200 || $responseCode >= 300) {
            throw new ClientException($responseCode, $result);
        }

        curl_close($ch);

        return $result;
    }
}
