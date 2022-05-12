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
        if (!array_key_exists($this->name, $data)) {
            if ($this->serdeType->getIsNullable()) {
                return null;
            } else {
                $type = $this->serdeType->displayName();
                throw new SerializationException("Cannot instantiate prop $this->name of type $type because there is no data for the key $this->name");
            }
        }
        $propData = $data[$this->name];
        unset($data[$this->name]);
        return $this->serdeType->denormalize($propData, $serializer);
    }
}