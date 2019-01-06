<?php

namespace WireMock\Client;

class HttpWait
{
    public function waitForServerToGive200($url, $timeoutSecs = 10)
    {
        $debugTrace = array();
        $startTime = microtime(true);
        $serverStarted = false;
        while (microtime(true) - $startTime < $timeoutSecs) {
            try {
                $headers = @get_headers($url, 1);
            } catch (\Exception $e) {
                $debugTrace[] = "$url not yet up. Error getting headers: " . $e->getMessage();
                continue;
            }
            if (isset($headers) && isset($headers[0]) && strpos($headers[0], '200 OK') !== false) {
                $serverStarted = true;
                break;
            } else {
                if (!isset($headers)) {
                    $debugTrace[] = "$url not yet up. \$headers not set";
                } else if (!isset($headers[0])) {
                    $debugTrace[] = "$url not yet up. \$headers[0] not set";
                } else {
                    $debugTrace[] = "$url not yet up. \$headers[0] was ${headers[0]}";
                }
            }
            usleep(100000);
        }
        if (!$serverStarted) {
            $time = microtime(true) - $startTime;
            $debugTrace[] = "$url failed to come up after $time seconds";
            echo implode("\n", $debugTrace) ."\n";
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
