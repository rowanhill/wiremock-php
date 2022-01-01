<?php

namespace WireMock\Client;

class Curl
{
    /**
     * @param string $url
     * @return string The response body
     * @throws ClientException
     */
    public function get($url)
    {
        return $this->makeCurlRequest('GET', $url);
    }

    /**
     * @param string $url
     * @param array|null $jsonArray
     * @return string The response body
     * @throws ClientException
     */
    public function post($url, array $jsonArray = null)
    {
        return $this->makeCurlRequest('POST', $url, $jsonArray);
    }

    /**
     * @param string $url
     * @param array|null $jsonArray
     * @return string The response body
     * @throws ClientException
     */
    public function put($url, array $jsonArray = null)
    {
        return $this->makeCurlRequest('PUT', $url, $jsonArray);
    }

    /**
     * @param string $url
     * @return string The response body
     * @throws ClientException
     */
    public function delete($url)
    {
        return $this->makeCurlRequest('DELETE', $url);
    }

    private function makeCurlRequest($method, $url, $jsonArray = null)
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
