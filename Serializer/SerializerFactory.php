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

use Symfony\Component\Serializer\SerializerInterface;

use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyFactory;
use JMS\SerializerBundle\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\SerializerBundle\Serializer\Exclusion\DisjunctExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\VersionExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\NoneExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\AllExclusionStrategy;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Serializer;

class SerializerFactory
{
    private $reader;
    private $propertyNamingStrategy;
    private $encoders;

    public function __construct(AnnotationReader $reader, PropertyNamingStrategyInterface $propertyNamingStrategy, array $encoders)
    {
        $this->reader = $reader;
        $this->propertyNamingStrategy = $propertyNamingStrategy;
        $this->encoders = $encoders;
    }

    public function configureSerializer(SerializerInterface $serializer, $version = null)
    {
        if (null === $version) {
            $strategies = array(
                'ALL'  => new AllExclusionStrategy($this->reader),
                'NONE' => new NoneExclusionStrategy($this->reader),
            );
        } else {
            $versionStrategy = new VersionExclusionStrategy($this->reader, $version);
            $strategies = array(
                'ALL'  => new DisjunctExclusionStrategy(array(
                    $versionStrategy, new AllExclusionStrategy($this->reader)
                )),
                'NONE' => new DisjunctExclusionStrategy(array(
                    $versionStrategy, new NoneExclusionStrategy($this->reader),
                )),
            );
        }

        $serializer->addNormalizer(new AnnotatedNormalizer($this->reader, $this->propertyNamingStrategy, new ExclusionStrategyFactory($strategies)));
        foreach ($this->encoders as $format => $encoder) {
            $serializer->setEncoder($format, $encoder);
        }
    }

    public function getSerializer($version = null)
    {
        $serializer = new Serializer();
        $this->configureSerializer($serializer, $version);

        return $serializer;
    }
}