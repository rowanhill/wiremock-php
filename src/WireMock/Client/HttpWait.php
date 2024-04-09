<?php

namespace WireMock\Client;

class HttpWait
{
    /**
     * @var Curl
     */
    private $curl;

    public function __construct(Curl $curl = null)
    {
        $this->curl = $curl ?? new Curl();
    }

    public function waitForServerToGive200($url, $timeoutSecs = 10, $debug = true)
    {
        $debugTrace = array();
        $startTime = microtime(true);
        $serverStarted = false;
        while (microtime(true) - $startTime < $timeoutSecs) {
            try {
                $this->curl->get($url);
                $serverStarted = true;
                break;
            } catch (\Exception $e) {
                $debugTrace[] = "$url not yet up. " . $e->getMessage();

                usleep(100000);

                continue;
            }
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
