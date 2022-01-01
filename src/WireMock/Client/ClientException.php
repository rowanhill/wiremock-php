<?php

namespace WireMock\Client;

class ClientException extends \Exception
{
    private $_responseCode;
    private $_response;

    public function __construct(int $responseCode, string $response)
    {
        $message = 'WireMock server returned a non-2xx status';
        $nestedMessages = [];
        $responseArray = json_decode($response, true);
        if ($responseArray && isset($responseArray['errors']) && is_array($responseArray['errors'])) {
            $errors = $responseArray['errors'];
            foreach ($errors as $error) {
                $nestedMessagePieces = [];
                if (isset($error['code'])) {
                    $nestedMessagePieces[] = 'Code ' . $error['code'];
                }
                if (isset($error['title'])) {
                    $nestedMessagePieces[] = $error['title'];
                }
                $nestedMessagePieces = ['  ' . join(': ', $nestedMessagePieces)];
                if (isset($error['detail'])) {
                    $nestedMessagePieces[] = $error['detail'];
                }
                $nestedMessages[] = join("\n    >  ", $nestedMessagePieces);
            }
        }
        if (!empty($nestedMessages)) {
            $message .= ". Errors were:\n";
            $message .= join("\n", $nestedMessages);
        }
        parent::__construct($message);
        $this->_responseCode = $responseCode;
        $this->_response = $response;
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->_responseCode;
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->_response;
    }
}