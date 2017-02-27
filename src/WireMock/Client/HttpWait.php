<?php

namespace WireMock\Client;

class HttpWait
{
    public function waitForServerToGive200($url, $timeoutSecs = 5)
    {
        $startTime = microtime(true);
        $serverStarted = false;
        while (microtime(true) - $startTime < $timeoutSecs) {
            try {
                $headers = @get_headers($url, 1);
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

    public function waitForServerToFailToRespond($url, $timeoutSecs = 5)
    {
        $startTime = microtime(true);
        $serverFailedToRespond = false;
        while (microtime(true) - $startTime < $timeoutSecs) {
            if (@get_headers($url, 1) === false) {
                $serverFailedToRespond = true;
                break;
            }
        }
        return $serverFailedToRespond;
    }
}
