<?php

namespace WireMock\Client;

class LoggedRequest
{
    /** @var string */
    private $url;
    /** @var string */
    private $absoluteUrl;
    /** @var string */
    private $method;
    /** @var string */
    private $clientIp;
    private $headers;
    private $cookies;
    private $body;
    private $bodyAsBase64;
    private $browserProxyRequest;
    private $loggedDate;
    private $loggedDateString;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getAbsoluteUrl()
    {
        return $this->absoluteUrl;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getBodyAsBase64()
    {
        return $this->bodyAsBase64;
    }

    /**
     * @return boolean
     */
    public function isBrowserProxyRequest()
    {
        return $this->browserProxyRequest;
    }

    /**
     * @return int
     */
    public function getLoggedDate()
    {
        return $this->loggedDate;
    }

    /**
     * @return string
     */
    public function getLoggedDateString()
    {
        return $this->loggedDateString;
    }
}
