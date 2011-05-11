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

use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyFactoryInterface;
use JMS\SerializerBundle\Serializer\Naming\PropertyNamingStrategyInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
use JMS\SerializerBundle\Annotation\ExclusionPolicy;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AnnotatedNormalizer extends SerializerAwareNormalizer
{
    private $reader;
    private $propertyNamingStrategy;
    private $exclusionStrategyFactory;

    public function __construct(AnnotationReader $reader, PropertyNamingStrategyInterface $propertyNamingStrategy, ExclusionStrategyFactoryInterface $exclusionStrategyFactory)
    {
        $this->reader = $reader;
        $this->propertyNamingStrategy = $propertyNamingStrategy;
        $this->exclusionStrategyFactory = $exclusionStrategyFactory;
    }

    public function normalize($object, $format = null)
    {
        // collect class hierarchy
        $class = new \ReflectionClass($object);
        $classes = $this->getClassHierarchy($class);

        // go through properties and collect values
        $normalized = $processed = array();
        foreach ($classes as $class) {
            $exclusionStrategy = $this->getExclusionStrategy($class);

            foreach ($class->getProperties() as $property) {
                if (isset($processed[$name = $property->getName()])) {
                    continue;
                }
                $processed[$name] = true;

                if ($exclusionStrategy->shouldSkipProperty($property)) {
                    continue;
                }

                $serializedName = $this->propertyNamingStrategy->translateName($property);
                $property->setAccessible(true);
                $normalized[$serializedName] = $this->normalizeValue($property->getValue($object), $format);
            }
        }

        return $normalized;
    }

    public function denormalize($data, $class, $format = null)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('$data must be an array.');
        }

        $class = new \ReflectionClass($class);
        $classes = $this->getClassHierarchy($class);

        $object = unserialize(sprintf('O:%d:"%s":0:{}', strlen($class->getName()), $class->getName()));
        $processed = array();
        foreach ($classes as $class) {
            $exclusionStrategy = $this->getExclusionStrategy($class);

            foreach ($class->getProperties() as $property) {
                if (isset($processed[$name = $property->getName()])) {
                    continue;
                }
                $processed[$name] = true;

                if ($exclusionStrategy->shouldSkipProperty($property)) {
                    continue;
                }

                $serializedName = $this->propertyNamingStrategy->translateName($property);
                if (!array_key_exists($serializedName, $data)) {
                    continue;
                }

                // FIXME: We need to let the user specify the type of this key
                throw new RuntimeException('This is not yet implemented.');
            }
        }
    }

    public function supportsNormalization($data, $format = null)
    {
        return true;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return true;
    }

    private function normalizeValue($value, $format)
    {
        if (is_array($value) || $value instanceof \Traversable) {
            $array = array();
            $isList = $this->isList($value);

            foreach ($value as $k => $v) {
                if ($isList) {
                    $array[] = $this->normalizeValue($v, $format);
                } else {
                    $array[$k] = $this->normalizeValue($v, $format);
                }
            }

            $value = $array;
        } else if (is_object($value)) {
            $value = $this->normalize($value, $format);
        }

        return $value;
    }

    private function isList($traversable)
    {
        foreach ($traversable as $k => $v) {
            if (!is_int($k)) {
                return false;
            }
        }

        return true;
    }

    private function getExclusionStrategy(\ReflectionClass $class)
    {
        $annotations = $this->reader->getClassAnnotations($class);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof ExclusionPolicy) {
                return $this->exclusionStrategyFactory->getStrategy($annotation->getStrategy());
            }
        }

        return $this->exclusionStrategyFactory->getStrategy('NONE');
    }

    private function getClassHierarchy(\ReflectionClass $class)
    {
        $classes = array();
        do {
            if (!$class->isUserDefined()) {
                break;
            }

            $classes[] = $class;
        } while (false !== $class = $class->getParentClass());

        return array_reverse($classes, false);
    }
}
