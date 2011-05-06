<?php

namespace JMS\SerializerBundle\Serializer\Normalizer;

use JMS\SerializerBundle\Serializer\InstanceCreatorInterface;
use JMS\SerializerBundle\Exception\UnsupportedException;

class NormalizableObjectNormalizer extends SerializerAwareNormalizer
{
    private $instanceCreator;

    public function __construct(InstanceCreatorInterface $instanceCreator)
    {
        $this->instanceCreator = $instanceCreator;
    }

    public function normalize($data, $format = null)
    {
        if (!$data instanceof NormalizableInterface) {
            throw new UnsupportedException('$data does not implement NormalizableInterface.');
        }

        return $data->normalize($this->serializer, $format);
    }

    public function denormalize($data, $type, $format = null)
    {
        $refl = new \ReflectionClass($type);
        if (!$refl->isSubclassOf('JMS\SerializerBundle\Serializer\Normalizer\NormalizableInterface')) {
            throw new UnsupportedException('$type does not implement NormalizableInterface.');
        }

        $object = $this->instanceCreator->createInstance($refl);
        $object->denormalize($this->serializer, $data, $format);

        return $object;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof NormalizableInterface;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        $refl = new \ReflectionClass($type);

        return $refl->isSubclassOf('JMS\SerializerBundle\Serializer\Normalizer\NormalizableInterface');
    }
}