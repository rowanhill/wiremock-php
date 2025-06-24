<?php

namespace WireMock\Client;

class LoggedRequest
{
    /** @var string */
    private $url; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var string */
    private $absoluteUrl; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var string */
    private $method; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var string */
    private $clientIp; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var array<string, string> */
    private $headers; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var array<string, string> */
    private $cookies; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var string */
    private $body; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var string */
    private $bodyAsBase64; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var boolean */
    private $browserProxyRequest; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var int */
    private $loggedDate; // @phpstan-ignore property.onlyRead (serde can write this)
    /** @var string */
    private $loggedDateString; // @phpstan-ignore property.onlyRead (serde can write this)

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
     * @return array<string, string>
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array<string, string>
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
     * @return bool
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
