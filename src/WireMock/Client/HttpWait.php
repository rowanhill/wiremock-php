<?php

namespace WireMock\Client;

class HttpWait
{
    public function waitForServerToGive200($url, $timeoutSecs = 10, $debug = false)
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
            if (isset($headers) && isset($headers[0]) && \in_array($headers[0], array('HTTP/1.1 200 OK', 'HTTP/1.0 200 OK'))) {
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
            if ($debug) {
                echo implode("\n", $debugTrace) ."\n";
            }
            echo "$url failed to come up after $time seconds";

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
