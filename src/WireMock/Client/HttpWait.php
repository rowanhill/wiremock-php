<?php

namespace WireMock\Client;

class HttpWait
{
    /**
     * @param string $url
     * @param int $timeoutSecs
     * @param bool $debug
     */
    public function waitForServerToGive200($url, $timeoutSecs = 10, $debug = true): bool
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

            if ($headers && isset($headers[0]) && strpos($headers[0], '200 OK') !== false) {
                $serverStarted = true;
                break;
            } else {
                if (!$headers) {
                    $debugTrace[] = "$url not yet up. \$headers not set";
                } else if (!isset($headers[0])) {
                    $debugTrace[] = "$url not yet up. \$headers[0] not set";
                } else {
                    $debugTrace[] = "$url not yet up. \$headers[0] was {$headers[0]}";
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

    /**
     * @param string $url
     * @param int $timeoutSecs
     */
    public function waitForServerToFailToRespond($url, $timeoutSecs = 5): bool
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
