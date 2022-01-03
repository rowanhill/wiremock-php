<?php

namespace WireMock\Serde;

use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class PrePostAmendingNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait {
        setSerializer as traitSetSerializer;
    }
    
    /** @var AbstractObjectNormalizer */
    private $_delegateNormalizer;

    /**
     * @param AbstractObjectNormalizer $delegateNormalizer
     */
    public function __construct($delegateNormalizer)
    {
        $this->_delegateNormalizer = $delegateNormalizer;
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->traitSetSerializer($serializer);
        $this->_delegateNormalizer->setSerializer($serializer);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $normalizedArray = $this->_delegateNormalizer->normalize($object, $format, $context);

        if ($object instanceof PostNormalizationAmenderInterface) {
            $normalizedArray = forward_static_call([get_class($object), 'amendNormalisation'], $normalizedArray, $object);
        }

        return $normalizedArray;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->_delegateNormalizer->supportsNormalization($data, $format);
    }
}