<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Serializer;

use JMS\SerializerBundle\Serializer\Normalizer\NormalizableInterface;

use JMS\SerializerBundle\Exception\RuntimeException;
use JMS\SerializerBundle\Serializer\SerializerAwareInterface;
use JMS\SerializerBundle\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Serializer implementation.
 *
 * This serializer distinuishes three different types of normalizers, one
 * normalizer for native php types, one default normalizer for objects, and an
 * arbitrary amount of specialized normalizers for specific object classes.
 *
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

    /**
     * {@inheritDoc}
     */
    public final function normalize($data, $format = null)
    {
        if (is_object($data) && $this->customObjectNormalizers) {
            foreach ($this->customObjectNormalizers as $normalizer) {
                if ($normalizer->supportsNormalization($data, $format)) {
                    return $normalizer->normalize($data, $format);
                }
            }
        }

        if ($this->nativePhpTypeNormalizer->supportsNormalization($data, $format)) {
            return $this->nativePhpTypeNormalizer->normalize($data, $format);
        }

        return $this->defaultObjectNormalizer->normalize($data, $format);
    }

    /**
     * {@inheritDoc}
     */
    public final function denormalize($data, $type, $format = null)
    {
        if ($this->nativePhpTypeNormalizer->supportsDenormalization($data, $type, $format)) {
            return $this->nativePhpTypeNormalizer->denormalize($data, $type, $format);
        }

        if ($this->customObjectNormalizers) {
            foreach ($this->customObjectNormalizers as $normalizer) {
                if ($normalizer->supportsDenormalization($data, $type, $format)) {
                    return $normalizer->denormalize($data, $type, $format);
                }
            }
        }

        return $this->defaultObjectNormalizer->denormalize($data, $type, $format);
    }

    /**
     * {@inheritDoc}
     */
    public final function serialize($data, $format)
    {
        $data = $this->normalize($data, $format);

        return $this->encode($data, $format);
    }

    /**
     * {@inheritDoc}
     */
    public final function deserialize($data, $type, $format)
    {
        $data = $this->decode($data, $format);

        return $this->denormalize($data, $type, $format);
    }

    /**
     * {@inheritDoc}
     */
    public final function encode($data, $format)
    {
        return $this->getEncoder($format)->encode($data, $format);
    }

    /**
     * {@inheritDoc}
     */
    public final function decode($data, $format)
    {
        return $this->getEncoder($format)->decode($data, $format);
    }

    protected function getEncoder($format)
    {
        if (!isset($this->encoderMap[$format])) {
            throw new RuntimeException(sprintf('No encoder found for format "%s".', $format));
        }

        return $this->encoderMap[$format];
    }
}
