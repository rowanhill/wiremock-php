<?php

namespace WireMock\Serde;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class PrePostAmendingNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
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
            $normalizedArray = forward_static_call([get_class($object), 'amendPostNormalisation'], $normalizedArray, $object);
        }

        return $normalizedArray;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->_delegateNormalizer->supportsNormalization($data, $format);
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (is_subclass_of($type, PreDenormalizationAmenderInterface::class)) {
            $data = forward_static_call([$type, 'amendPreDenormalisation'], $data);
        }
        if (is_subclass_of($type, ObjectToPopulateFactoryInterface::class)) {
            /** @var ObjectToPopulateResult $result */
            $result = forward_static_call([$type, 'createObjectToPopulate'], $data, $this->serializer, $format, $context);
            if ($result->object == null) {
                return null;
            }
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $result->object;
            $data = $result->normalisedArray;
        }
        
        return $this->_delegateNormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->_delegateNormalizer->supportsDenormalization($data, $type, $format);
    }
}