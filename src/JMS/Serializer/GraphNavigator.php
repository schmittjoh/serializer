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

use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\EventDispatcher\Event;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;

/**
 * Handles traversal along the object graph.
 *
 * This class handles traversal along the graph, and calls different methods
 * on visitors, or custom handlers to process its nodes.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class GraphNavigator
{
    const DIRECTION_SERIALIZATION = 1;
    const DIRECTION_DESERIALIZATION = 2;

    private $dispatcher;
    private $metadataFactory;
    private $handlerRegistry;
    private $objectConstructor;

    /**
     * Parses a direction string to one of the direction constants.
     *
     * @param string $dirStr
     *
     * @return integer
     */
    public static function parseDirection($dirStr)
    {
        switch (strtolower($dirStr)) {
            case 'serialization':
                return self::DIRECTION_SERIALIZATION;

            case 'deserialization':
                return self::DIRECTION_DESERIALIZATION;

            default:
                throw new InvalidArgumentException(sprintf('The direction "%s" does not exist.', $dirStr));
        }
    }

    public function __construct(MetadataFactoryInterface $metadataFactory, HandlerRegistryInterface $handlerRegistry, ObjectConstructorInterface $objectConstructor, EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
        $this->metadataFactory = $metadataFactory;
        $this->handlerRegistry = $handlerRegistry;
        $this->objectConstructor = $objectConstructor;
    }

    /**
     * Called for each node of the graph that is being traversed.
     *
     * @param mixed $data the data depends on the direction, and type of visitor
     * @param null|array $type array has the format ["name" => string, "params" => array]
     * @param VisitorInterface $visitor
     *
     * @return mixed the return value depends on the direction, and type of visitor
     */
    public function accept($data, array $type = null, Context $context)
    {
        $isSerializing = $context->getDirection() === GraphNavigator::DIRECTION_SERIALIZATION;
        $visitor = $context->getVisitor();

        // If the type was not given, we infer the most specific type from the
        // input data in serialization mode.
        if (null === $type) {
            if ( ! $isSerializing) {
                $msg = 'The type must be given for all properties when deserializing.';
                if (null !== $path = $context->getPath()) {
                    $msg .= ' Path: '.$path;
                }

                throw new RuntimeException($msg);
            }

            $typeName = gettype($data);
            if ('object' === $typeName) {
                $typeName = get_class($data);
            }

            $type = array('name' => $typeName, 'params' => array());
        }
        // If the data is null, we have to force the type to null regardless of the input in order to
        // guarantee correct handling of null values, and not have any internal auto-casting behavior.
        else if ($isSerializing && null === $data) {
            $type = array('name' => 'NULL', 'params' => array());
        }

        switch ($type['name']) {
            case 'NULL':
                return $visitor->visitNull($data, $type, $context);

            case 'string':
                return $visitor->visitString($data, $type, $context);

            case 'integer':
                return $visitor->visitInteger($data, $type, $context);

            case 'boolean':
                return $visitor->visitBoolean($data, $type, $context);

            case 'double':
            case 'float':
                return $visitor->visitDouble($data, $type, $context);

            case 'array':
                return $visitor->visitArray($data, $type, $context);

            case 'resource':
                $msg = 'Resources are not supported in serialized data.';
                if (null !== $path = $context->getPath()) {
                    $msg .= ' Path: '.$path;
                }

                throw new RuntimeException($msg);

            default:
                if ($isSerializing && null !== $data) {
                    if ($context->isVisiting($data)) {
                        return null;
                    }
                    $context->startVisiting($data);
                }

                // Trigger pre-serialization callbacks, and listeners if they exist.
                // Dispatch pre-serialization event before handling data to have ability change type in listener
                if ($isSerializing) {
                    if (null !== $this->dispatcher && $this->dispatcher->hasListeners('serializer.pre_serialize', $type['name'], $context->getFormat())) {
                        $this->dispatcher->dispatch('serializer.pre_serialize', $type['name'], $context->getFormat(), $event = new PreSerializeEvent($context, $data, $type));
                        $type = $event->getType();
                    }
                }

                // First, try whether a custom handler exists for the given type. This is done
                // before loading metadata because the type name might not be a class, but
                // could also simply be an artifical type.
                if (null !== $handler = $this->handlerRegistry->getHandler($context->getDirection(), $type['name'], $context->getFormat())) {
                    $rs = call_user_func($handler, $visitor, $data, $type, $context);

                    $context->stopVisiting($data);

                    return $rs;
                }

                $exclusionStrategy = $context->getExclusionStrategy();

                /** @var $metadata ClassMetadata */
                $metadata = $this->metadataFactory->getMetadataForClass($type['name']);
                if (null !== $exclusionStrategy && $exclusionStrategy->shouldSkipClass($metadata, $context)) {
                    $context->stopVisiting($data);

                    return null;
                }

                if ($isSerializing) {
                    foreach ($metadata->preSerializeMethods as $method) {
                        /** @var $method \ReflectionMethod */

                        $method->invoke($data);
                    }
                }

                $object = $data;
                if ( ! $isSerializing) {
                    $object = $this->objectConstructor->construct($visitor, $metadata, $data, $type);
                }

                if (isset($metadata->handlerCallbacks[$context->getDirection()][$context->getFormat()])) {
                    $rs = $object->{$metadata->handlerCallbacks[$context->getDirection()][$context->getFormat()]}($visitor, $isSerializing ? null : $data);
                    $this->afterVisitingObject($metadata, $object, $type, $context);

                    return $isSerializing ? $rs : $object;
                }

                $visitor->startVisitingObject($metadata, $object, $type, $context);
                foreach ($metadata->propertyMetadata as $propertyMetadata) {
                    if (null !== $exclusionStrategy && $exclusionStrategy->shouldSkipProperty($propertyMetadata, $context)) {
                        continue;
                    }

                    if ( ! $isSerializing && $propertyMetadata->readOnly) {
                        continue;
                    }

                    $visitor->visitProperty($propertyMetadata, $data, $context);
                }

                if ($isSerializing) {
                    $this->afterVisitingObject($metadata, $data, $type, $context);

                    return $visitor->endVisitingObject($metadata, $data, $type, $context);
                }

                $rs = $visitor->endVisitingObject($metadata, $data, $type, $context);
                $this->afterVisitingObject($metadata, $rs, $type, $context);

                return $rs;
        }
    }

    private function afterVisitingObject(ClassMetadata $metadata, $object, array $type, Context $context)
    {
        if ($context->getDirection() === self::DIRECTION_SERIALIZATION) {
            $context->stopVisiting($object);

            foreach ($metadata->postSerializeMethods as $method) {
                $method->invoke($object);
            }

            if (null !== $this->dispatcher && $this->dispatcher->hasListeners('serializer.post_serialize', $metadata->name, $context->getFormat())) {
                $this->dispatcher->dispatch('serializer.post_serialize', $metadata->name, $context->getFormat(), new Event($context, $object, $type));
            }

            return;
        }

        foreach ($metadata->postDeserializeMethods as $method) {
            $method->invoke($object);
        }

        if (null !== $this->dispatcher && $this->dispatcher->hasListeners('serializer.post_deserialize', $metadata->name, $context->getFormat())) {
            $this->dispatcher->dispatch('serializer.post_deserialize', $metadata->name, $context->getFormat(), new Event($context, $object, $type));
        }
    }
}
