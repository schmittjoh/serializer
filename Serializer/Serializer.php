<?php

namespace JMS\SerializerBundle\Serializer;

use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Serializer implements SerializerInterface
{
    private $nativePhpTypeNormalizer;
    private $customObjectNormalizers;
    private $defaultObjectNormalizer;
    private $encoderMap;

    public function __construct(NormalizerInterface $nativePhpNormalizer, NormalizerInterface $defaultObjectNormalizer, array $customObjectNormalizers = array(), array $encoderMap = array())
    {
        if ($nativePhpNormalizer instanceof SerializerAwareInterface) {
            $nativePhpNormalizer->setSerializer($this);
        }
        $this->nativePhpTypeNormalizer = $nativePhpNormalizer;

        if ($defaultObjectNormalizer instanceof SerializerAwareInterface) {
            $defaultObjectNormalizer->setSerializer($this);
        }
        $this->defaultObjectNormalizer = $defaultObjectNormalizer;

        foreach ($customObjectNormalizers as $normalizer) {
            if ($normalizer instanceof SerializerAwareInterface) {
                $normalizer->setSerializer($this);
            }
        }
        $this->customObjectNormalizers = $customObjectNormalizers;

        foreach ($encoderMap as $encoder) {
            if ($encoder instanceof SerializerAwareInterface) {
                $encoder->setSerializer($this);
            }
        }
        $this->encoderMap = $encoderMap;
    }

    public final function normalize($data, $format = null)
    {
        if ($this->nativePhpTypeNormalizer->supportsNormalization($data, $format)) {
            return $this->nativePhpTypeNormalizer->normalize($data, $format);
        }

        foreach ($this->customObjectNormalizers as $normalizer) {
            if ($normalizer->supportsNormalization($data, $format)) {
                return $normalizer->normalize($data, $format);
            }
        }

        return $this->defaultObjectNormalizer->normalize($data, $format);
    }

    public final function denormalize($data, $type, $format = null)
    {
        if ($this->nativePhpTypeNormalizer->supportsDenormalization($data, $type, $format)) {
            return $this->nativePhpTypeNormalizer->denormalize($data, $type, $format);
        }

        foreach ($this->customObjectNormalizers as $normalizer) {
            if ($normalizer->supportsDenormalization($data, $type, $format)) {
                return $normalizer->denormalize($data, $type, $format);
            }
        }

        return $this->defaultObjectNormalizer->denormalize($data, $type, $format);
    }

    public final function serialize($data, $format)
    {
        $data = $this->normalize($data, $format);

        return $this->encode($data, $format);
    }

    public final function deserialize($data, $type, $format)
    {
        $data = $this->decode($data, $format);

        return $this->denormalize($data, $type, $format);
    }

    public final function encode($data, $format)
    {
        return $this->getEncoder($format)->encode($data, $format);
    }

    public final function decode($data, $format)
    {
        return $this->getEncoder($format)->decode($data, $format);
    }

    protected function getEncoder($format)
    {
        if (!isset($this->encoderMap[$format])) {
            throw new \RuntimeException(sprintf('No encoder found for format "%s".', $format));
        }

        return $this->encoderMap[$format];
    }
}