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

namespace JMS\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\Exception\UnsupportedFormatException;
use Metadata\MetadataFactoryInterface;
use JMS\Serializer\Exclusion\VersionExclusionStrategy;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use PhpCollection\MapInterface;

/**
 * Serializer Implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Serializer implements SerializerInterface
{
    private $factory;
    private $handlerRegistry;
    private $objectConstructor;
    private $dispatcher;
    private $typeParser;

    /** @var \PhpCollection\MapInterface */
    private $serializationVisitors;

    /** @var \PhpCollection\MapInterface */
    private $deserializationVisitors;
    private $exclusionStrategy;
    private $serializeNull;

    /**
     * Constructor.
     *
     * @param \Metadata\MetadataFactoryInterface $factory
     * @param Handler\HandlerRegistryInterface $handlerRegistry
     * @param Construction\ObjectConstructorInterface $objectConstructor
     * @param \PhpCollection\MapInterface<VisitorInterface> $serializationVisitors
     * @param \PhpCollection\MapInterface<VisitorInterface> $deserializationVisitors
     * @param EventDispatcher\EventDispatcherInterface $dispatcher
     * @param TypeParser $typeParser
     */
    public function __construct(MetadataFactoryInterface $factory, HandlerRegistryInterface $handlerRegistry, ObjectConstructorInterface $objectConstructor, MapInterface $serializationVisitors, MapInterface $deserializationVisitors, EventDispatcherInterface $dispatcher = null, TypeParser $typeParser = null)
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
        if ( ! $this->serializationVisitors->containsKey($format)) {
            throw new UnsupportedFormatException(sprintf('The format "%s" is not supported for serialization.', $format));
        }

        $visitor = $this->serializationVisitors->get($format)->get();
        $visitor->setSerializeNull($this->serializeNull);
        $visitor->setNavigator($navigator = new GraphNavigator(GraphNavigator::DIRECTION_SERIALIZATION, $this->factory, $format, $this->handlerRegistry, $this->objectConstructor, $this->exclusionStrategy, $this->dispatcher));
        $navigator->accept($visitor->prepare($data), null, $visitor);

        return $visitor->getResult();
    }

    public function deserialize($data, $type, $format)
    {
        if ( ! $this->deserializationVisitors->containsKey($format)) {
            throw new UnsupportedFormatException(sprintf('The format "%s" is not supported for deserialization.', $format));
        }

        $visitor = $this->deserializationVisitors->get($format)->get();
        $visitor->setNavigator($navigator = new GraphNavigator(GraphNavigator::DIRECTION_DESERIALIZATION, $this->factory, $format, $this->handlerRegistry, $this->objectConstructor, $this->exclusionStrategy, $this->dispatcher));
        $navigatorResult = $navigator->accept($visitor->prepare($data), $this->typeParser->parse($type), $visitor);

        // This is a special case if the root is handled by a callback on the object iself.
        if ((null === $visitorResult = $visitor->getResult()) && null !== $navigatorResult) {
            return $navigatorResult;
        }

        return $visitorResult;
    }
}
