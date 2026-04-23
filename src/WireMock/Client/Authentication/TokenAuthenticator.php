<?php

namespace WireMock\Client\Authentication;

final class TokenAuthenticator implements Authenticator
{
    /**
     * @var non-empty-string
     */
    private $token;

    /**
     * @param non-empty-string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function modifyHeaders(array $headers): array
    {
        return array_merge($headers, [sprintf('Authorization: Token %s', $this->token)]);
    }

    public function modifyUrl(string $url): string
    {
        return $url;
    }
}