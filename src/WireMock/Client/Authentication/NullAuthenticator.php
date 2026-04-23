<?php

namespace WireMock\Client\Authentication;

final class NullAuthenticator implements Authenticator
{
    public function modifyHeaders(array $headers): array
    {
        return $headers;
    }

    public function modifyUrl(string $url): string
    {
        return $url;
    }
}