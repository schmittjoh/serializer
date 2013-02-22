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

    private $navigator;
    private $defaultContext;

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
    public function __construct(MetadataFactoryInterface $factory, HandlerRegistryInterface $handlerRegistry, ObjectConstructorInterface $objectConstructor, MapInterface $serializationVisitors, MapInterface $deserializationVisitors, EventDispatcherInterface $dispatcher = null, TypeParser $typeParser = null, Context $defaultContext = null)
    {
        $this->factory = $factory;
        $this->handlerRegistry = $handlerRegistry;
        $this->objectConstructor = $objectConstructor;
        $this->dispatcher = $dispatcher;
        $this->typeParser = $typeParser ?: new TypeParser();
        $this->serializationVisitors = $serializationVisitors;
        $this->deserializationVisitors = $deserializationVisitors;
        $this->defaultContext = $defaultContext ?: new Context();

        $this->navigator = new GraphNavigator($this->factory, $this->handlerRegistry, $this->objectConstructor, $this->dispatcher);
    }

    public function serialize($data, $format, Context $context = null)
    {
        if ( ! $this->serializationVisitors->containsKey($format)) {
            throw new UnsupportedFormatException(sprintf('The format "%s" is not supported for serialization.', $format));
        }

        if (null === $context) {
            $context = $this->defaultContext;
        }

        $context->initialize(
            GraphNavigator::DIRECTION_SERIALIZATION,
            $format,
            $visitor = $this->serializationVisitors->get($format)->get(),
            $this->navigator
        );

        $visitor->setNavigator($this->navigator);
        $this->navigator->accept($visitor->prepare($data), null, $context);

        return $visitor->getResult();
    }

    public function deserialize($data, $type, $format, Context $context = null)
    {
        if ( ! $this->deserializationVisitors->containsKey($format)) {
            throw new UnsupportedFormatException(sprintf('The format "%s" is not supported for deserialization.', $format));
        }

        if (null === $context) {
            $context = $this->defaultContext;
        }

        $context->initialize(
            GraphNavigator::DIRECTION_DESERIALIZATION,
            $format,
            $visitor = $this->deserializationVisitors->get($format)->get(),
            $this->navigator
        );

        $visitor->setNavigator($this->navigator);
        $navigatorResult = $this->navigator->accept($visitor->prepare($data), $this->typeParser->parse($type), $context);

        // This is a special case if the root is handled by a callback on the object iself.
        if ((null === $visitorResult = $visitor->getResult()) && null !== $navigatorResult) {
            return $navigatorResult;
        }

        return $visitorResult;
    }
}
