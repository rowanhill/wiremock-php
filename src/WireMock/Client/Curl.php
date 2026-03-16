<?php

namespace WireMock\Client;

use http\Exception\RuntimeException;

class Curl
{
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
     * @param string|null $body The request body
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
     * @param non-empty-string $method
     * @param string $url
     * @return string
     * @throws ClientException
     */
    private function makeCurlRequest(string $method, string $url, ?string $json = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($json !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            $contentLength = strlen($json);
        } else {
            $contentLength = 0;
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Content-Length: $contentLength",
        ));

        $result = curl_exec($ch);
        assert($result !== true, 'if CURLOPT_RETURNTRANSFER is set, this is never true');

        if ($result === false) {
            throw new RuntimeException('curl_exec failed (returned FALSE)');
        }

        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($responseCode < 200 || $responseCode >= 300) {
            throw new ClientException($responseCode, $result);
        }

        if (PHP_VERSION_ID < 80000) {
            curl_close($ch);
        }

        return $result;
    }
}
