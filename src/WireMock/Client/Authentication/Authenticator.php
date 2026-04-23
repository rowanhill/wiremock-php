<?php

namespace WireMock\Client\Authentication;

interface Authenticator
{
    /**
     * @param list<non-empty-string> $headers
     * @return list<non-empty-string>
     */
    public function modifyHeaders(array $headers): array;

    /**
     * @param non-empty-string $url
     * @return non-empty-string
     */
    public function modifyUrl(string $url): string;
}