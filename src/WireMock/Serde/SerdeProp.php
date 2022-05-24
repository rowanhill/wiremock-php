<?php

namespace WireMock\Serde;

use WireMock\Serde\Type\SerdeType;

class SerdeProp
{
    /** @var string */
    public $name;
    /** @var SerdeType */
    public $serdeType;

    /**
     * @param string $name
     * @param SerdeType $serdeType
     */
    public function __construct(string $name, SerdeType $serdeType)
    {
        $this->name = $name;
        $this->serdeType = $serdeType;
    }

    /**
     * @throws SerializationException
     */
    public function instantiateAndConsumeData(array &$data, Serializer $serializer)
    {
        $propData = array_key_exists($this->name, $data) ? $data[$this->name] : null;
        unset($data[$this->name]);
        return $this->serdeType->denormalize($propData, $serializer);
    }
}