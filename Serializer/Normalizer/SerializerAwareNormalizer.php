<?php

namespace JMS\SerializerBundle\Serializer\Normalizer;

use JMS\SerializerBundle\Serializer\SerializerInterface;
use JMS\SerializerBundle\Serializer\SerializerAwareInterface;

/**
 * SerializerAwareNormalizer base class.
 *
 * If you normalizer needs to work recursively, then you can extend this base
 * class to automatically receive the serializer instance.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class SerializerAwareNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    protected $serializer;

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
}
