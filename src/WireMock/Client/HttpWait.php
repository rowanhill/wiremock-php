<?php

namespace WireMock\Client;

class HttpWait
{
    function waitForServerToGive200($url, $timeoutSecs = 5)
    {
        $startTime = microtime(true);
        $serverStarted = false;
        while (microtime(true) - $startTime < $timeoutSecs) {
            try {
                $headers = get_headers($url, 1);
            } catch (\Exception $e) {
                continue;
            }
            if (isset($headers) && isset($headers[0]) && $headers[0] === 'HTTP/1.1 200 OK') {
                $serverStarted = true;
                break;
            }
        }
        return $serverStarted;
    }
}