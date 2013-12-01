<?php

namespace WireMock\Client;

class Curl
{
    function post($url, array $jsonArray)
    {
        $json = json_encode($jsonArray);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ));

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}