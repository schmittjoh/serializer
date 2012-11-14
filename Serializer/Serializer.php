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

use JMS\SerializerBundle\Serializer\Construction\ObjectConstructorInterface;
use JMS\SerializerBundle\Serializer\Handler\HandlerRegistryInterface;
use JMS\SerializerBundle\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\SerializerBundle\Exception\UnsupportedFormatException;
use Metadata\MetadataFactoryInterface;
use JMS\SerializerBundle\Serializer\Exclusion\VersionExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyInterface;

class Serializer implements SerializerInterface
{
    private $factory;
    private $handlerRegistry;
    private $objectConstructor;
    private $dispatcher;
    private $typeParser;
    private $serializationVisitors;
    private $deserializationVisitors;
    private $exclusionStrategy;
    private $serializeNull;

    public function __construct(MetadataFactoryInterface $factory, HandlerRegistryInterface $handlerRegistry, ObjectConstructorInterface $objectConstructor, EventDispatcherInterface $dispatcher = null, TypeParser $typeParser = null, array $serializationVisitors = array(), array $deserializationVisitors = array())
    {
        $this->factory = $factory;
        $this->handlerRegistry = $handlerRegistry;
        $this->objectConstructor = $objectConstructor;
        $this->dispatcher = $dispatcher;
        $this->typeParser = $typeParser ?: new TypeParser();
        $this->serializationVisitors = $serializationVisitors;
        $this->deserializationVisitors = $deserializationVisitors;
        $this->serializeNull = false;
    }

    /**
     * @param boolean $serializeNull
     */
    public function setSerializeNull($serializeNull)
    {
        $this->serializeNull = $serializeNull;
    }

    public function setExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy = null)
    {
        $this->exclusionStrategy = $exclusionStrategy;
    }

    /**
     * @param integer $version
     */
    public function setVersion($version)
    {
        if (null === $version) {
            $this->exclusionStrategy = null;

            return;
        }

        $this->exclusionStrategy = new VersionExclusionStrategy($version);
    }

    /**
     * @param null|array $groups
     */
    public function setGroups($groups)
    {
        if ( ! $groups) {
            $this->exclusionStrategy = null;

            return;
        }

        $this->exclusionStrategy = new GroupsExclusionStrategy((array) $groups);
    }

    public function serialize($data, $format)
    {
        $visitor = $this->getSerializationVisitor($format);
        $visitor->setSerializeNull($this->serializeNull);
        $visitor->setNavigator($navigator = new GraphNavigator(GraphNavigator::DIRECTION_SERIALIZATION, $this->factory, $format, $this->handlerRegistry, $this->objectConstructor, $this->exclusionStrategy, $this->dispatcher));
        $navigator->accept($visitor->prepare($data), null, $visitor);

        return $visitor->getResult();
    }

    public function deserialize($data, $type, $format)
    {
        $visitor = $this->getDeserializationVisitor($format);
        $visitor->setNavigator($navigator = new GraphNavigator(GraphNavigator::DIRECTION_DESERIALIZATION, $this->factory, $format, $this->handlerRegistry, $this->objectConstructor, $this->exclusionStrategy, $this->dispatcher));
        $navigatorResult = $navigator->accept($visitor->prepare($data), $this->typeParser->parse($type), $visitor);

        // This is a special case if the root is handled by a callback on the object iself.
        if ((null === $visitorResult = $visitor->getResult()) && null !== $navigatorResult) {
            return $navigatorResult;
        }

        return $visitorResult;
    }

    /**
     * @return VisitorInterface
     */
    public function getDeserializationVisitor($format)
    {
        if (!isset($this->deserializationVisitors[$format])) {
            throw new UnsupportedFormatException(sprintf('Unsupported format "%s".', $format));
        }

        return $this->deserializationVisitors[$format];
    }

    /**
     * @return VisitorInterface
     */
    public function getSerializationVisitor($format)
    {
        if (!isset($this->serializationVisitors[$format])) {
            throw new UnsupportedFormatException(sprintf('Unsupported format "%s".', $format));
        }

        return $this->serializationVisitors[$format];
    }
}
