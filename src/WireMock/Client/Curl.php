<?php

namespace WireMock\Client;

class Curl
{
    /**
     * @param string $url
     * @param array $jsonArray
     * @return string The response body
     */
    public function post($url, array $jsonArray = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
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

        curl_close($ch);

        return $result;
    }

    /**
     * @param string $url
     * @param array $jsonArray
     * @return string The response body
     */
    public function put($url, array $jsonArray = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
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

        curl_close($ch);

        return $result;
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function delete($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Content-Length: 0",
        ));

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}
