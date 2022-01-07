<?php

namespace WireMock\Client;

use WireMock\Serde\DummyConstructorArgsObjectToPopulateFactory;
use WireMock\Serde\ObjectToPopulateFactoryInterface;

class BasicCredentials implements ObjectToPopulateFactoryInterface
{
    use DummyConstructorArgsObjectToPopulateFactory;

    /** @var string */
    private $_username;
    /** @var string */
    private $_password;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'username' => $this->_username,
            'password' => $this->_password
        );
    }

    public static function fromArray(array $array)
    {
        return new BasicCredentials($array['username'], $array['password']);
    }
}