<?php

declare(strict_types=1);

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

use JMS\Parser\AbstractParser;
use JMS\Serializer\ContextFactory\DefaultDeserializationContextFactory;
use JMS\Serializer\ContextFactory\DefaultSerializationContextFactory;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exception\UnsupportedFormatException;
use JMS\Serializer\GraphNavigator\Factory\GraphNavigatorFactoryInterface;
use JMS\Serializer\Visitor\Factory\DeserializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\SerializationVisitorFactory;
use Metadata\MetadataFactoryInterface;

/**
 * Serializer Implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class Serializer implements SerializerInterface, ArrayTransformerInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $factory;

    /**
     * @var TypeParser
     */
    private $typeParser;

    /**
     * @var array|SerializationVisitorFactory[]
     */
    private $serializationVisitors = [];

    /**
     * @var array|DeserializationVisitorFactory[]
     */
    private $deserializationVisitors = [];

    /**
     * @var SerializationContextFactoryInterface
     */
    private $serializationContextFactory;

    /**
     * @var DeserializationContextFactoryInterface
     */
    private $deserializationContextFactory;

    /**
     * @var array|GraphNavigatorFactoryInterface[]
     */
    private $graphNavigators;

    /**
     * @param MetadataFactoryInterface $factory
     * @param array|GraphNavigatorFactoryInterface[] $graphNavigators
     * @param SerializationVisitorFactory[] $serializationVisitors
     * @param DeserializationVisitorFactory[] $deserializationVisitors
     * @param SerializationContextFactoryInterface|null $serializationContextFactory
     * @param DeserializationContextFactoryInterface|null $deserializationContextFactory
     * @param AbstractParser|null $typeParser
     */
    public function __construct(
        MetadataFactoryInterface $factory,
        array $graphNavigators,
        array $serializationVisitors,
        array $deserializationVisitors,
        SerializationContextFactoryInterface $serializationContextFactory = null,
        DeserializationContextFactoryInterface $deserializationContextFactory = null,
        AbstractParser $typeParser = null
    ) {
        $this->factory = $factory;
        $this->graphNavigators = $graphNavigators;
        $this->serializationVisitors = $serializationVisitors;
        $this->deserializationVisitors = $deserializationVisitors;

        $this->typeParser = $typeParser ?: new TypeParser();

        $this->serializationContextFactory = $serializationContextFactory ?: new DefaultSerializationContextFactory();
        $this->deserializationContextFactory = $deserializationContextFactory ?: new DefaultDeserializationContextFactory();
    }

    /**
     * Parses a direction string to one of the direction constants.
     *
     * @param string $dirStr
     *
     * @return integer
     */
    public static function parseDirection(string $dirStr): int
    {
        switch (strtolower($dirStr)) {
            case 'serialization':
                return GraphNavigatorInterface::DIRECTION_SERIALIZATION;

            case 'deserialization':
                return GraphNavigatorInterface::DIRECTION_DESERIALIZATION;

            default:
                throw new InvalidArgumentException(sprintf('The direction "%s" does not exist.', $dirStr));
        }
    }

    private function findInitialType(?string $type, SerializationContext $context)
    {
        if ($type !== null) {
            return $type;
        } elseif ($context->hasAttribute('initial_type')) {
            return $context->getAttribute('initial_type');
        }
        return null;
    }

    private function getNavigator(int $direction): GraphNavigatorInterface
    {
        if (!isset($this->graphNavigators[$direction])) {
            throw new RuntimeException(
                sprintf(
                    'Can not find a graph navigator for the direction "%s".',
                    $direction === GraphNavigatorInterface::DIRECTION_SERIALIZATION ? 'serialization' : 'deserialization'
                )
            );
        }

        return $this->graphNavigators[$direction]->getGraphNavigator();
    }

    private function getVisitor(int $direction, string $format): VisitorInterface
    {
        $factories = $direction === GraphNavigatorInterface::DIRECTION_SERIALIZATION
            ? $this->serializationVisitors
            : $this->deserializationVisitors;

        if (!isset($factories[$format])) {
            throw new UnsupportedFormatException(
                sprintf(
                    'The format "%s" is not supported for %s.', $format,
                    $direction === GraphNavigatorInterface::DIRECTION_SERIALIZATION ? 'serialization' : 'deserialization'
            ));
        }

        return $factories[$format]->getVisitor();
    }

    public function serialize($data, string $format, SerializationContext $context = null, string $type = null): string
    {
        if (null === $context) {
            $context = $this->serializationContextFactory->createSerializationContext();
        }

        $visitor = $this->getVisitor(GraphNavigatorInterface::DIRECTION_SERIALIZATION, $format);
        $navigator = $this->getNavigator(GraphNavigatorInterface::DIRECTION_SERIALIZATION);

        $type = $this->findInitialType($type, $context);

        $result = $this->visit($navigator, $visitor, $context, $data, $format, $type);
        return $visitor->getResult($result);
    }

    public function deserialize(string $data, string $type, string $format, DeserializationContext $context = null)
    {
        if (null === $context) {
            $context = $this->deserializationContextFactory->createDeserializationContext();
        }

        $visitor = $this->getVisitor(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, $format);
        $navigator = $this->getNavigator(GraphNavigatorInterface::DIRECTION_DESERIALIZATION);

        $result = $this->visit($navigator, $visitor, $context, $data, $format, $type);

        return $visitor->getResult($result);
    }

    /**
     * {@InheritDoc}
     */
    public function toArray($data, SerializationContext $context = null, string $type = null): array
    {
        if (null === $context) {
            $context = $this->serializationContextFactory->createSerializationContext();
        }

        $visitor = $this->getVisitor(GraphNavigatorInterface::DIRECTION_SERIALIZATION, 'json');
        $navigator = $this->getNavigator(GraphNavigatorInterface::DIRECTION_SERIALIZATION);

        $type = $this->findInitialType($type, $context);
        $result = $this->visit($navigator, $visitor, $context, $data, 'json', $type);
        $result = $this->convertArrayObjects($result);

        if (!\is_array($result)) {
            throw new RuntimeException(sprintf(
                'The input data of type "%s" did not convert to an array, but got a result of type "%s".',
                \is_object($data) ? \get_class($data) : \gettype($data),
                \is_object($result) ? \get_class($result) : \gettype($result)
            ));
        }

        return $result;
    }

    /**
     * {@InheritDoc}
     */
    public function fromArray(array $data, string $type, DeserializationContext $context = null)
    {
        if (null === $context) {
            $context = $this->deserializationContextFactory->createDeserializationContext();
        }

        $visitor = $this->getVisitor(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, 'json');
        $navigator = $this->getNavigator(GraphNavigatorInterface::DIRECTION_DESERIALIZATION);

        return $this->visit($navigator, $visitor, $context, $data, 'json', $type, false);
    }

    private function visit(GraphNavigatorInterface $navigator, VisitorInterface $visitor, Context $context, $data, string $format, string $type = null, bool $prepare = true)
    {
        $context->initialize(
            $format,
            $visitor,
            $navigator,
            $this->factory
        );

        $visitor->setNavigator($navigator);
        $navigator->initialize($visitor, $context);

        if ($prepare) {
            $data = $visitor->prepare($data);
        }

        if ($type !== null) {
            $type = $this->typeParser->parse($type);
        }
        return $navigator->accept($data, $type);
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
    public function getMetadataFactory(): MetadataFactoryInterface
    {
        return $this->factory;
    }
}
