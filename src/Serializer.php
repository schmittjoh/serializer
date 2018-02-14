<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\ContextFactory\DefaultDeserializationContextFactory;
use JMS\Serializer\ContextFactory\DefaultSerializationContextFactory;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exception\UnsupportedFormatException;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use Metadata\MetadataFactoryInterface;

/**
 * Serializer Implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Serializer implements SerializerInterface, ArrayTransformerInterface
{
    private $factory;
    private $handlerRegistry;
    private $objectConstructor;
    private $dispatcher;
    private $typeParser;

    private $serializationVisitors = array();

    private $deserializationVisitors = array();

    private $navigator;

    /**
     * @var SerializationContextFactoryInterface
     */
    private $serializationContextFactory;

    /**
     * @var DeserializationContextFactoryInterface
     */
    private $deserializationContextFactory;

    /**
     * Constructor.
     *
     * @param \Metadata\MetadataFactoryInterface $factory
     * @param Handler\HandlerRegistryInterface $handlerRegistry
     * @param Construction\ObjectConstructorInterface $objectConstructor
     * @param VisitorInterface[] $serializationVisitors
     * @param VisitorInterface[] $deserializationVisitors
     * @param EventDispatcherInterface $dispatcher
     * @param TypeParser $typeParser
     * @param ExpressionEvaluatorInterface|null $expressionEvaluator
     */
    public function __construct(
        MetadataFactoryInterface $factory,
        HandlerRegistryInterface $handlerRegistry,
        ObjectConstructorInterface $objectConstructor,
        array $serializationVisitors,
        array $deserializationVisitors,
        EventDispatcherInterface $dispatcher = null,
        TypeParser $typeParser = null,
        ExpressionEvaluatorInterface $expressionEvaluator = null,
        SerializationContextFactoryInterface $serializationContextFactory = null,
        DeserializationContextFactoryInterface $deserializationContextFactory = null

    )
    {
        $this->factory = $factory;
        $this->handlerRegistry = $handlerRegistry;
        $this->objectConstructor = $objectConstructor;
        $this->dispatcher = $dispatcher ?: new EventDispatcher();
        $this->typeParser = $typeParser ?: new TypeParser();
        $this->serializationVisitors = $serializationVisitors;
        $this->deserializationVisitors = $deserializationVisitors;

        $this->navigator = new GraphNavigator($this->factory, $this->handlerRegistry, $this->objectConstructor, $this->dispatcher, $expressionEvaluator);

        $this->serializationContextFactory = $serializationContextFactory ?: new DefaultSerializationContextFactory();
        $this->deserializationContextFactory = $deserializationContextFactory ?: new DefaultDeserializationContextFactory();
    }

    public function serialize($data, $format, SerializationContext $context = null)
    {
        if (null === $context) {
            $context = $this->serializationContextFactory->createSerializationContext();
        }

        if (!isset($this->serializationVisitors[$format])) {
            throw new UnsupportedFormatException(sprintf('The format "%s" is not supported for serialization.', $format));
        }

        return (function (VisitorInterface $visitor) use ($context, $data, $format) {
                $type = $context->getInitialType() !== null ? $this->typeParser->parse($context->getInitialType()) : null;

                $this->visit($visitor, $context, $visitor->prepare($data), $format, $type);

                return $visitor->getResult();
            })($this->serializationVisitors[$format]);
    }

    public function deserialize($data, $type, $format, DeserializationContext $context = null)
    {
        if (null === $context) {
            $context = $this->deserializationContextFactory->createDeserializationContext();
        }

        if (!isset($this->deserializationVisitors[$format])) {
            throw new UnsupportedFormatException(sprintf('The format "%s" is not supported for deserialization.', $format));
        }

        return (function (VisitorInterface $visitor) use ($context, $data, $format, $type) {
            $preparedData = $visitor->prepare($data);
            $navigatorResult = $this->visit($visitor, $context, $preparedData, $format, $this->typeParser->parse($type));

            return $this->handleDeserializeResult($visitor->getResult(), $navigatorResult);
        })($this->deserializationVisitors[$format]);
    }

    /**
     * {@InheritDoc}
     */
    public function toArray($data, SerializationContext $context = null)
    {
        if (null === $context) {
            $context = $this->serializationContextFactory->createSerializationContext();
        }

        if (!isset($this->serializationVisitors['json'])) {
            throw new UnsupportedFormatException(sprintf('The format "%s" is not supported for fromArray.', 'json'));
        }

        return (function (JsonSerializationVisitor $visitor) use ($context, $data) {
                $type = $context->getInitialType() !== null ? $this->typeParser->parse($context->getInitialType()) : null;

                $this->visit($visitor, $context, $data, 'json', $type);
                $result = $this->convertArrayObjects($visitor->getRoot());

                if (!\is_array($result)) {
                    throw new RuntimeException(sprintf(
                        'The input data of type "%s" did not convert to an array, but got a result of type "%s".',
                        \is_object($data) ? \get_class($data) : \gettype($data),
                        \is_object($result) ? \get_class($result) : \gettype($result)
                    ));
                }

                return $result;
            })($this->serializationVisitors['json']);
    }

    /**
     * {@InheritDoc}
     */
    public function fromArray(array $data, $type, DeserializationContext $context = null)
    {
        if (null === $context) {
            $context = $this->deserializationContextFactory->createDeserializationContext();
        }

        if (!isset($this->serializationVisitors['json'])) {
            throw new UnsupportedFormatException(sprintf('The format "%s" is not supported for fromArray.', 'json'));
        }

        return (function (JsonDeserializationVisitor $visitor) use ($data, $type, $context) {
                $navigatorResult = $this->visit($visitor, $context, $data, 'json', $this->typeParser->parse($type));

                return $this->handleDeserializeResult($visitor->getResult(), $navigatorResult);
            })($this->deserializationVisitors['json']);
    }

    private function visit(VisitorInterface $visitor, Context $context, $data, $format, array $type = null)
    {
        $context->initialize(
            $format,
            $visitor,
            $this->navigator,
            $this->factory
        );

        $visitor->setNavigator($this->navigator);

        return $this->navigator->accept($data, $type, $context);
    }

    private function handleDeserializeResult($visitorResult, $navigatorResult)
    {
        // This is a special case if the root is handled by a callback on the object itself.
        if (null === $visitorResult && null !== $navigatorResult) {
            return $navigatorResult;
        }

        return $visitorResult;
    }

    private function convertArrayObjects($data)
    {
        if ($data instanceof \ArrayObject || $data instanceof \stdClass) {
            $data = (array)$data;
        }
        if (\is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->convertArrayObjects($v);
            }
        }

        return $data;
    }

    /**
     * @return MetadataFactoryInterface
     */
    public function getMetadataFactory()
    {
        return $this->factory;
    }
}
