<?php

namespace WireMock\Client;

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
     * @param array|null $jsonArray
     * @return string The response body
     * @throws ClientException
     */
    public function post(string $url, array $jsonArray = null): string
    {
        return $this->makeCurlRequest('POST', $url, $jsonArray);
    }

    /**
     * @param string $url
     * @param array|null $jsonArray
     * @return string The response body
     * @throws ClientException
     */
    public function put(string $url, array $jsonArray = null): string
    {
        return $this->makeCurlRequest('PUT', $url, $jsonArray);
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

    private function makeCurlRequest(string $method, string $url, $jsonArray = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($jsonArray !== null) {
            $json = json_encode($jsonArray);
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

        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($responseCode < 200 || $responseCode >= 300) {
            throw new ClientException($responseCode, $result);
        }

        curl_close($ch);

        return $result;
    }
}
