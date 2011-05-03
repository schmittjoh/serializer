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

namespace JMS\SerializerBundle\Serializer\Normalizer;

use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyInterface;

use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
use JMS\SerializerBundle\Annotation\Type;
use JMS\SerializerBundle\Exception\UnsupportedException;
use Annotations\ReaderInterface;
use JMS\SerializerBundle\Exception\InvalidArgumentException;
use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyFactoryInterface;
use JMS\SerializerBundle\Serializer\Naming\PropertyNamingStrategyInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use JMS\SerializerBundle\Annotation\ExclusionPolicy;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PropertyBasedNormalizer extends SerializerAwareNormalizer
{
    private $reader;
    private $propertyNamingStrategy;
    private $exclusionStrategyFactory;
    private $exclusionStrategies = array();
    private $reflectionData = array();
    private $translatedNames = array();
    private $excludedProperties = array();

    public function __construct(ReaderInterface $reader, PropertyNamingStrategyInterface $propertyNamingStrategy, ExclusionStrategyFactoryInterface $exclusionStrategyFactory)
    {
        $this->reader = $reader;
        $this->propertyNamingStrategy = $propertyNamingStrategy;
        $this->exclusionStrategyFactory = $exclusionStrategyFactory;
    }

    public function normalize($object, $format = null)
    {
        if (!is_object($object)) {
            throw new UnsupportedException(sprintf('Type "%s" is not supported.', gettype($object)));
        }

        // collect class hierarchy
        list($class, $classes) = $this->getReflectionData(get_class($object));

        // go through properties and collect values
        $normalized = $processed = array();
        foreach ($classes as $class) {
            $exclusionStrategy = $this->getExclusionStrategy($class);

            foreach ($class->getProperties() as $property) {
                if (isset($processed[$name = $property->getName()])) {
                    continue;
                }
                $processed[$name] = true;

                if ($this->shouldSkipProperty($exclusionStrategy, $property)) {
                    continue;
                }

                $serializedName = $this->translateName($property);
                $property->setAccessible(true);
                $value = $this->serializer->normalize($property->getValue($object), $format);

                if (null === $value) {
                    continue;
                }

                $normalized[$serializedName] = $value;
            }

        }

        return $normalized;
    }

    public function supportsNormalization($data, $format = null)
    {
        return is_object($data);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type);
    }

    public function denormalize($data, $type, $format = null)
    {
        if (!class_exists($type)) {
            throw new UnsupportedException(sprintf('Unsupported type; "%s" is not a valid class.', $type));
        }

        list($class, $classes) = $this->getReflectionData($type);

        $object = unserialize(sprintf('O:%d:"%s":0:{}', strlen($type), $type));
        $processed = array();
        foreach ($classes as $class) {
            $exclusionStrategy = $this->getExclusionStrategy($class);

            foreach ($class->getProperties() as $property) {
                if (isset($processed[$name = $property->getName()])) {
                    continue;
                }
                $processed[$name] = true;


                if ($this->shouldSkipProperty($exclusionStrategy, $property)) {
                    continue;
                }

                $serializedName = $this->translateName($property);
                if (!array_key_exists($serializedName, $data)) {
                    continue;
                }

                $type = null;
                foreach ($this->reader->getPropertyAnnotations($property) as $annot) {
                    if ($annot instanceof Type) {
                        $type = $annot->getName();
                        break;
                    }
                }
                if (null === $type) {
                    throw new RuntimeException(sprintf('You need to add a "@Type" annotation for property "%s" in class "%s".', $property->getName(), $property->getDeclaringClass()->getName()));
                }

                $value = $this->serializer->denormalize($data[$serializedName], $type, $format);
                $property->setAccessible(true);
                $property->setValue($object, $value);
            }
        }

        return $object;
    }

    private function translateName(\ReflectionProperty $property)
    {
        $key = $property->getDeclaringClass()->getName().'$'.$property->getName();
        if (isset($this->translatedNames[$key])) {
            return $this->translatednames[$key];
        }

        return $this->translatedNames[$key] = $this->propertyNamingStrategy->translateName($property);
    }

    private function shouldSkipProperty(ExclusionStrategyInterface $exclusionStrategy, \ReflectionProperty $property)
    {
        $key = $property->getDeclaringClass()->getName().'$'.$property->getName();
        if (isset($this->excludedProperties[$key])) {
            return $this->excludedProperties[$key];
        }

        $this->excludedProperties[$key] = $exclusionStrategy->shouldSkipProperty($property);
    }

    private function getExclusionStrategy(\ReflectionClass $class)
    {
        if (isset($this->exclusionStrategies[$name = $class->getName()])) {
            return $this->exclusionStrategies[$name];
        }

        $annotations = $this->reader->getClassAnnotations($class);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof ExclusionPolicy) {
                return $this->exclusionStrategyFactory->getStrategy($annotation->getStrategy());
            }
        }

        return $this->exclusionStrategies[$name] = $this->exclusionStrategyFactory->getStrategy('NONE');
    }

    private function getReflectionData($fqcn)
    {
        if (isset($this->reflectionData[$fqcn])) {
            return $this->reflectionData[$fqcn];
        }

        $class = new \ReflectionClass($fqcn);
        $classes = array();
        do {
            if (!$class->isUserDefined()) {
                break;
            }

            $classes[] = $class;
        } while (false !== $class = $class->getParentClass());
        $classes = array_reverse($classes, false);

        return $this->reflectionData[$fqcn] = array($class, $classes);
    }
}