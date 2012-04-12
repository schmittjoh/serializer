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

use JMS\SerializerBundle\Exception\UnsupportedFormatException;
use Metadata\MetadataFactoryInterface;
use JMS\SerializerBundle\Exception\InvalidArgumentException;
use JMS\SerializerBundle\Serializer\Exclusion\VersionExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyInterface;

class Serializer implements SerializerInterface
{
    private $factory;
    private $serializationVisitors;
    private $deserializationVisitors;
    private $exclusionStrategy;

    public function __construct(MetadataFactoryInterface $factory, array $serializationVisitors = array(), array $deserializationVisitors = array())
    {
        $this->factory = $factory;
        $this->serializationVisitors = $serializationVisitors;
        $this->deserializationVisitors = $deserializationVisitors;
    }

    public function setExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy = null)
    {
        $this->exclusionStrategy = $exclusionStrategy;
    }

    public function setVersion($version)
    {
        if (null === $version) {
            $this->exclusionStrategy = null;

            return;
        }

        $this->exclusionStrategy = new VersionExclusionStrategy($version);
    }
    
    public function setGroups($groups)
    {
        if (!$groups) {
            $this->exclusionStrategy = null;

            return;
        }

        $this->exclusionStrategy = new GroupsExclusionStrategy((array) $groups);
    }

    public function serialize($data, $format)
    {
        $visitor = $this->getSerializationVisitor($format);
        $visitor->setNavigator($navigator = new GraphNavigator(GraphNavigator::DIRECTION_SERIALIZATION, $this->factory, $this->exclusionStrategy));
        $navigator->accept($visitor->prepare($data), null, $visitor);

        return $visitor->getResult();
    }

    public function deserialize($data, $type, $format)
    {
        $visitor = $this->getDeserializationVisitor($format);
        $visitor->setNavigator($navigator = new GraphNavigator(GraphNavigator::DIRECTION_DESERIALIZATION, $this->factory, $this->exclusionStrategy));
        $navigator->accept($visitor->prepare($data), $type, $visitor);

        return $visitor->getResult();
    }

    protected function getDeserializationVisitor($format)
    {
        if (!isset($this->deserializationVisitors[$format])) {
            throw new UnsupportedFormatException(sprintf('Unsupported format "%s".', $format));
        }

        return $this->deserializationVisitors[$format];
    }

    protected function getSerializationVisitor($format)
    {
        if (!isset($this->serializationVisitors[$format])) {
            throw new UnsupportedFormatException(sprintf('Unsupported format "%s".', $format));
        }

        return $this->serializationVisitors[$format];
    }
}
